<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use App\Models\ServiceLog;
use App\Models\ServiceReminder;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Servis & Smart Odometer
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vehicles = $user ? $user->vehicles : collect();

        // Ambil kendaraan aktif dari session atau kendaraan pertama
        $activeVehicleId = session('active_vehicle_id');
        $activeVehicle = $vehicles->firstWhere('id', $activeVehicleId) ?: $vehicles->first();

        // Greeting waktu (Pagi/Siang/Sore/Malam)
        $hour = (int) now()->format('H');
        if ($hour >= 4 && $hour < 11) {
            $timeGreeting = 'Selamat pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $timeGreeting = 'Selamat siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $timeGreeting = 'Selamat sore';
        } else {
            $timeGreeting = 'Selamat malam';
        }

        $userName = $user ? explode(' ', $user->name)[0] : 'Rider';

        if (!$activeVehicle) {
            return view('pages.dashboard', [
                'vehicles' => $vehicles,
                'activeVehicle' => null,
                'timeGreeting' => $timeGreeting,
                'userName' => $userName,
                'parts' => collect(),
                'closestPart' => null,
                'calendarEvents' => [],
                'maintenanceItems' => MaintenanceItem::all(),
            ]);
        }

        // Ambil data kesehatan sparepart (5 Part Vital)
        $rawParts = $activeVehicle->parts_health;
        $reminders = ServiceReminder::where('vehicle_id', $activeVehicle->id)->get();
        $dailyAvg = max(1.0, (float) ($activeVehicle->daily_average_km ?: 15.0));

        // Format dan tambahkan data prediksi tanggal serta icon
        $parts = $rawParts->map(function ($part) use ($reminders, $dailyAvg, $activeVehicle) {
            $reminder = $reminders->firstWhere('maintenance_item_id', $part->id);

            // Hitung estimasi hari tersisa berdasarkan sisa KM dibagi rata-rata harian
            $daysRemaining = max(0, (int) ceil($part->km_remaining / $dailyAvg));
            $calculatedDate = now()->addDays($daysRemaining);

            // Jika ada pengingat khusus yang dijadwalkan secara manual oleh user, prioritaskan
            $scheduledDate = ($reminder && $reminder->scheduled_date) 
                ? Carbon::parse($reminder->scheduled_date) 
                : $calculatedDate;

            // Mapping icon Lucide yang sesuai
            $icon = match ($part->id) {
                1 => 'droplet',        // Oli Mesin
                2 => 'disc',           // Oli Gardan
                3 => 'zap',            // Busi
                4 => 'shield-alert',   // Kampas Rem
                5 => 'refresh-cw',     // V-Belt
                6 => 'wind',           // Filter Udara
                default => (str_contains(strtolower($part->name), 'filter') ? 'wind' : 'wrench'),
            };

            // Gunakan status murni dari Vehicle Model (>40% safe, 15-40% warning, <15% danger)
            $statusKey = $part->status; // 'danger', 'warning', 'safe'
            $statusLabel = match ($part->status) {
                'danger' => 'Kritis',
                'warning' => 'Waspada',
                default => 'Aman',
            };

            return (object) [
                'id' => $part->id,
                'name' => $part->name,
                'short_name' => $part->short_name,
                'icon' => $icon,
                'percentage' => $part->percentage,
                'km_remaining' => $part->km_remaining,
                'interval_km' => $part->interval_km,
                'status' => $statusKey,
                'status_label' => $statusLabel,
                'color' => $part->color,
                'stroke_color' => $part->stroke_color,
                'days_remaining' => $daysRemaining,
                'scheduled_date' => $scheduledDate,
                'predicted_date_formatted' => $scheduledDate->translatedFormat('d M Y'),
                'predicted_date_full' => $scheduledDate->translatedFormat('l, d F Y'),
                'scheduled_date_raw' => $scheduledDate->format('Y-m-d'),
                'reminder_id' => $reminder ? $reminder->id : null,
            ];
        });

        // Urutkan part berdasarkan tanggal servis terdekat
        $sortedParts = $parts->sortBy('scheduled_date')->values();
        $closestPart = $sortedParts->first();

        // Buat payload kalender untuk Alpine.js
        $calendarEvents = [];
        foreach ($parts as $part) {
            $dateKey = $part->scheduled_date->format('Y-m-d');
            $calendarEvents[$dateKey][] = [
                'id' => $part->id,
                'title' => $part->name,
                'short_name' => $part->short_name,
                'status' => $part->status,
                'status_label' => $part->status_label,
                'percentage' => $part->percentage,
                'km_remaining' => $part->km_remaining,
                'date_formatted' => $part->predicted_date_full,
            ];
        }

        $allMaintenanceItems = MaintenanceItem::all();

        // Ambil riwayat servis kendaraan aktif saat ini (dikelompokkan per tanggal/kunjungan nota)
        $rawLogs = ServiceLog::with('maintenanceItem')
            ->where('vehicle_id', $activeVehicle->id)
            ->orderBy('service_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $recentServiceLogs = $rawLogs->groupBy(function ($log) {
            return $log->service_date->format('Y-m-d') . '_' . $log->odometer_at_service . '_' . ($log->workshop_name ?? '');
        })->map(function ($items) {
            $first = $items->first();
            $partNames = $items->pluck('maintenanceItem.item_name')->filter()->values();
            $totalCost = $items->sum('actual_cost');
            $receiptImageUrl = $items->pluck('receipt_image_url')->filter()->first();

            return (object) [
                'id' => $first->id,
                'service_date' => $first->service_date->format('Y-m-d'),
                'service_date_formatted' => $first->service_date->translatedFormat('d F Y'),
                'odometer_at_service' => $first->odometer_at_service,
                'actual_cost' => $totalCost,
                'workshop_name' => $first->workshop_name ?: 'Bengkel Mandiri',
                'notes' => $items->pluck('notes')->filter()->first(),
                'part_names' => $partNames,
                'receipt_image_url' => $receiptImageUrl,
                'is_verified' => !empty($receiptImageUrl),
                'item_count' => $items->count(),
            ];
        })->values();

        return view('pages.dashboard', compact(
            'vehicles',
            'activeVehicle',
            'timeGreeting',
            'userName',
            'parts',
            'closestPart',
            'calendarEvents',
            'allMaintenanceItems',
            'recentServiceLogs'
        ));
    }

    /**
     * Simpan atau update reminder jadwal servis khusus komponen
     */
    public function storeOrUpdateReminder(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_item_id' => 'required|exists:maintenance_items,id',
            'scheduled_date' => 'required|date',
            'custom_km' => 'nullable|integer|min:0',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        ServiceReminder::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $validated['maintenance_item_id'],
            ],
            [
                'scheduled_date' => $validated['scheduled_date'],
                'custom_km' => $validated['custom_km'] ?? null,
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Jadwal servis berhasil diperbarui!');
    }

    /**
     * Quick Service: Catat servis selesai langsung dari kartu komponen dashboard
     */
    public function quickService(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_item_id' => 'required|exists:maintenance_items,id',
            'service_date' => 'required|date',
            'odometer_at_service' => 'required|integer|min:0',
            'actual_cost' => 'required|numeric|min:0',
            'workshop_name' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $item = MaintenanceItem::findOrFail($validated['maintenance_item_id']);

        // Catat ke ServiceLog
        ServiceLog::create([
            'vehicle_id' => $vehicle->id,
            'maintenance_item_id' => $item->id,
            'service_date' => $validated['service_date'],
            'odometer_at_service' => $validated['odometer_at_service'],
            'actual_cost' => $validated['actual_cost'],
            'workshop_name' => $validated['workshop_name'] ?: 'Bengkel Mandiri',
            'notes' => $validated['notes'] ?? 'Servis cepat via Dashboard Servisa.',
        ]);

        // Jika odometer saat servis lebih tinggi dari last_recalibrate_km, update juga kalibrasi ODO
        if ($validated['odometer_at_service'] > ($vehicle->last_recalibrate_km ?: $vehicle->initial_odometer_km)) {
            $vehicle->update([
                'last_recalibrate_km' => $validated['odometer_at_service'],
                'last_recalibrate_date' => $validated['service_date'],
            ]);
        }

        // Reset atau perbarui pengingat berikutnya sesuai interval part
        $nextScheduledDate = Carbon::parse($validated['service_date'])->addMonths($item->interval_months ?: 2);
        ServiceReminder::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $item->id,
            ],
            [
                'scheduled_date' => $nextScheduledDate->toDateString(),
                'custom_km' => (int) $validated['odometer_at_service'] + ($item->interval_km ?: 2500),
                'status' => 'pending',
                'is_snoozed' => false,
            ]
        );

        return back()->with('success', 'Servis ' . $item->item_name . ' berhasil dicatat! Umur komponen telah kembali 100% prima.');
    }
}
