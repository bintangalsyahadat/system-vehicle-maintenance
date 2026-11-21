<?php

namespace App\Http\Controllers;

use App\Models\AiScanHistory;
use App\Models\MaintenanceItem;
use App\Models\ServiceLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Tampilkan halaman Riwayat Servis & Diagnosa AI untuk kendaraan tertentu
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vehicles = $user ? $user->vehicles : collect();

        // Cek jika ada parameter vehicle_id dari tombol di garasi (?vehicle_id=...)
        $requestedVehicleId = $request->query('vehicle_id');

        if ($requestedVehicleId && $vehicles->contains('id', $requestedVehicleId)) {
            $activeVehicleId = (int) $requestedVehicleId;
            session(['active_vehicle_id' => $activeVehicleId]);
        } else {
            // Ambil kendaraan aktif dari session atau kendaraan pertama milik user
            $activeVehicleId = session('active_vehicle_id');
        }

        $activeVehicle = $vehicles->firstWhere('id', $activeVehicleId) ?: $vehicles->first();

        $groupedLogs = collect();
        $aiScans = collect();
        $maintenanceItems = MaintenanceItem::all();

        if ($activeVehicle) {
            $rawLogs = ServiceLog::with('maintenanceItem')
                ->where('vehicle_id', $activeVehicle->id)
                ->orderBy('service_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // Grouping catatan servis berdasarkan tanggal + odometer + nama bengkel yang sama (1 Nota / Kunjungan)
            $groupedLogs = $rawLogs->groupBy(function ($log) {
                return $log->service_date . '_' . $log->odometer_at_service . '_' . ($log->workshop_name ?? '');
            })->map(function ($items) {
                $first = $items->first();
                $partNames = $items->pluck('maintenanceItem.item_name')->filter()->values();
                $partIds = $items->pluck('maintenance_item_id')->filter()->values();
                $totalCost = $items->sum('actual_cost');

                return (object) [
                    'id' => $first->id,
                    'service_date' => $first->service_date,
                    'odometer_at_service' => $first->odometer_at_service,
                    'actual_cost' => $totalCost,
                    'workshop_name' => $first->workshop_name,
                    'notes' => $items->pluck('notes')->filter()->first(),
                    'part_names' => $partNames,
                    'part_ids' => $partIds,
                    'item_count' => $items->count(),
                ];
            })->values();

            $aiScans = AiScanHistory::where('vehicle_id', $activeVehicle->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Kalkulasi Statistik Riwayat
        $totalCost = $groupedLogs->sum('actual_cost');
        $totalServices = $groupedLogs->count();
        $totalAiScans = $aiScans->count();

        // Skor Paspor Servis (Berdasarkan rasio keterawatan)
        $passportScore = $totalServices > 0 ? 'Terawat Baik' : 'Belum Ada Servis';

        return view('pages.history', compact(
            'vehicles',
            'activeVehicle',
            'groupedLogs',
            'aiScans',
            'maintenanceItems',
            'totalCost',
            'totalServices',
            'totalAiScans',
            'passportScore'
        ));
    }

    /**
     * Simpan entri catatan servis bengkel baru (service_logs)
     * Mendukung multi-komponen dalam 1 kali servis / nota
     */
    public function storeLog(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_item_ids' => 'nullable|array',
            'maintenance_item_ids.*' => 'exists:maintenance_items,id',
            'service_date' => 'required|date',
            'odometer_at_service' => 'required|integer|min:0',
            'actual_cost' => 'required|numeric|min:0',
            'workshop_name' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $selectedItemIds = $validated['maintenance_item_ids'] ?? [];
        $totalCost = (float) $validated['actual_cost'];
        $workshopName = $validated['workshop_name'] ?: 'Bengkel Mandiri';
        $serviceDate = $validated['service_date'];
        $odometerAtService = (int) $validated['odometer_at_service'];
        $notes = $validated['notes'] ?? null;

        if (!empty($selectedItemIds)) {
            // Jika ada beberapa sparepart yang diganti dalam 1 kunjungan servis,
            // catat record untuk masing-masing item agar riwayat part dan sisa umur ter-reset tepat.
            // Biaya dicatat penuh pada entri pertama, sedangkan entri berikutnya 0 agar total cost akurat.
            foreach ($selectedItemIds as $index => $itemId) {
                ServiceLog::create([
                    'vehicle_id' => $vehicle->id,
                    'maintenance_item_id' => $itemId,
                    'service_date' => $serviceDate,
                    'odometer_at_service' => $odometerAtService,
                    'actual_cost' => ($index === 0) ? $totalCost : 0,
                    'workshop_name' => $workshopName,
                    'notes' => $notes,
                ]);
            }
        } else {
            // Servis umum / berkala tanpa penggantian spesifik
            ServiceLog::create([
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => null,
                'service_date' => $serviceDate,
                'odometer_at_service' => $odometerAtService,
                'actual_cost' => $totalCost,
                'workshop_name' => $workshopName,
                'notes' => $notes,
            ]);
        }

        // 2. Kalibrasi ulang Odometer jika KM servis lebih besar dari recalibrate terakhir
        if ($odometerAtService >= $vehicle->current_odometer) {
            $vehicle->update([
                'last_recalibrate_km' => $odometerAtService,
                'last_recalibrate_date' => $serviceDate,
            ]);
        }

        return back()->with('success', 'Catatan servis berhasil ditambahkan ke Paspor Servis!');
    }

    /**
     * Tandai status riwayat diagnosa AI sebagai terselesaikan (Resolved)
     */
    public function resolveAiScan(AiScanHistory $aiScan)
    {
        if ($aiScan->vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        $aiScan->update(['is_resolved' => true]);

        return back()->with('success', 'Status diagnosa AI berhasil ditandai sebagai terselesaikan!');
    }

    /**
     * Ekspor / Cetak Dokumen Paspor Servis Resmi
     */
    public function printPassport(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        $rawLogs = ServiceLog::with('maintenanceItem')
            ->where('vehicle_id', $vehicle->id)
            ->orderBy('service_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Grouping untuk cetak paspor PDF
        $groupedLogs = $rawLogs->groupBy(function ($log) {
            return $log->service_date . '_' . $log->odometer_at_service . '_' . ($log->workshop_name ?? '');
        })->map(function ($items) {
            $first = $items->first();
            $partNames = $items->pluck('maintenanceItem.item_name')->filter()->values();
            $totalCost = $items->sum('actual_cost');

            return (object) [
                'id' => $first->id,
                'service_date' => $first->service_date,
                'odometer_at_service' => $first->odometer_at_service,
                'actual_cost' => $totalCost,
                'workshop_name' => $first->workshop_name,
                'notes' => $items->pluck('notes')->filter()->first(),
                'part_names' => $partNames,
            ];
        })->values();

        $totalCost = $groupedLogs->sum('actual_cost');
        $totalServices = $groupedLogs->count();

        return view('pdf.service-passport', compact('vehicle', 'groupedLogs', 'totalCost', 'totalServices'));
    }
}
