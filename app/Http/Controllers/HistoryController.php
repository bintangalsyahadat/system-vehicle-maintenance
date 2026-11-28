<?php

namespace App\Http\Controllers;

use App\Models\AiScanHistory;
use App\Models\MaintenanceItem;
use App\Models\ServiceLog;
use App\Models\Vehicle;
use App\Services\GeminiVisionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            // Grouping catatan servis berdasarkan tanggal + odometer + nama bengkel (1 Kunjungan / 1 Nota)
            $groupedLogs = $rawLogs->groupBy(function ($log) {
                return $log->service_date->format('Y-m-d') . '_' . $log->odometer_at_service . '_' . ($log->workshop_name ?? '');
            })->map(function ($items, $groupKey) {
                $first = $items->first();
                $partNames = $items->pluck('maintenanceItem.item_name')->filter()->values();
                $partIds = $items->pluck('maintenance_item_id')->filter()->values();
                $totalCost = $items->sum('actual_cost');
                $receiptImageUrl = $items->pluck('receipt_image_url')->filter()->first();

                // Detailed items list for editing / breakdown
                $detailedItems = $items->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'maintenance_item_id' => $log->maintenance_item_id,
                        'name' => $log->maintenanceItem ? $log->maintenanceItem->item_name : ($log->notes ?: 'Jasa / Part Lainnya'),
                        'cost' => (float) $log->actual_cost,
                        'notes' => $log->notes,
                    ];
                })->values();

                return (object) [
                    'group_key' => $groupKey,
                    'first_id' => $first->id,
                    'log_ids' => $items->pluck('id')->values()->all(),
                    'service_date' => $first->service_date->format('Y-m-d'),
                    'service_date_formatted' => $first->service_date->translatedFormat('d F Y'),
                    'odometer_at_service' => $first->odometer_at_service,
                    'actual_cost' => $totalCost,
                    'workshop_name' => $first->workshop_name ?: 'Bengkel Mandiri',
                    'notes' => $items->pluck('notes')->filter()->first(),
                    'part_names' => $partNames,
                    'part_ids' => $partIds,
                    'item_count' => $items->count(),
                    'receipt_image_url' => $receiptImageUrl,
                    'detailed_items' => $detailedItems,
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
     * Endpoint AJAX AI Vision: Scan Nota Servis Bengkel
     */
    public function scanReceipt(Request $request, GeminiVisionService $visionService)
    {
        $request->validate([
            'receipt_image' => 'required|string', // Base64 Data URL
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $imageData = $request->input('receipt_image');

        if (!preg_match('/^data:((?:image\/[a-zA-Z0-9.+]+|application\/pdf));base64,(.+)$/', $imageData, $matches)) {
            return response()->json([
                'success' => false,
                'message' => 'Format file gambar nota tidak valid.',
            ], 422);
        }

        $mimeType = $matches[1];
        $base64Image = $matches[2];

        // Simpan file fisik nota ke disk publik
        $extension = explode('/', $mimeType)[1] ?? 'jpg';
        if ($mimeType === 'application/pdf') {
            $extension = 'pdf';
        } elseif ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        $fileName = 'receipts/' . uniqid('receipt_') . '.' . $extension;
        Storage::disk('public')->put($fileName, base64_decode($base64Image));
        $publicUrl = Storage::disk('public')->url($fileName);

        // Ekstraksi data via Gemini Vision AI
        $result = $visionService->analyzeReceipt($base64Image, $mimeType, $vehicle);

        if (isset($result['is_valid_receipt']) && !$result['is_valid_receipt']) {
            Storage::disk('public')->delete($fileName);

            return response()->json([
                'success' => false,
                'message' => $result['rejection_reason'] ?: 'Gambar terdeteksi bukan nota atau faktur servis bengkel yang jelas.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'image_url' => $publicUrl,
            'data' => $result,
        ]);
    }

    /**
     * Simpan entri catatan servis bengkel baru (service_logs)
     * Mendukung multi-komponen / order lines dan foto bukti nota
     */
    public function storeLog(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_date' => 'required|date',
            'odometer_at_service' => 'required|integer|min:0',
            'workshop_name' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
            'receipt_image_url' => 'nullable|string|max:500',
            // Order lines
            'items' => 'nullable|array',
            'items.*.maintenance_item_id' => 'nullable',
            'items.*.item_name' => 'nullable|string|max:200',
            'items.*.cost' => 'nullable|numeric|min:0',
            // Legacy / simple fallback
            'maintenance_item_ids' => 'nullable|array',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $workshopName = $validated['workshop_name'] ?: 'Bengkel Mandiri';
        $serviceDate = $validated['service_date'];
        $odometerAtService = (int) $validated['odometer_at_service'];
        $notes = $validated['notes'] ?? null;
        $receiptImageUrl = $validated['receipt_image_url'] ?? null;

        // Cek apakah ada multi order lines
        $orderLines = $request->input('items', []);

        if (!empty($orderLines) && is_array($orderLines)) {
            foreach ($orderLines as $line) {
                $cost = isset($line['cost']) ? (float) $line['cost'] : 0;
                $itemId = !empty($line['maintenance_item_id']) ? (int) $line['maintenance_item_id'] : null;
                $itemName = $line['item_name'] ?? null;

                ServiceLog::create([
                    'vehicle_id' => $vehicle->id,
                    'maintenance_item_id' => $itemId,
                    'service_date' => $serviceDate,
                    'odometer_at_service' => $odometerAtService,
                    'actual_cost' => $cost,
                    'workshop_name' => $workshopName,
                    'notes' => $itemName ? ($itemName . ($notes ? ' | ' . $notes : '')) : $notes,
                    'receipt_image_url' => $receiptImageUrl,
                ]);
            }
        } else {
            // Mode fallback form standar
            $selectedItemIds = $validated['maintenance_item_ids'] ?? [];
            $totalCost = (float) ($validated['actual_cost'] ?? 0);

            if (!empty($selectedItemIds)) {
                $costPerItem = count($selectedItemIds) > 0 ? ($totalCost / count($selectedItemIds)) : $totalCost;
                foreach ($selectedItemIds as $itemId) {
                    ServiceLog::create([
                        'vehicle_id' => $vehicle->id,
                        'maintenance_item_id' => $itemId,
                        'service_date' => $serviceDate,
                        'odometer_at_service' => $odometerAtService,
                        'actual_cost' => $costPerItem,
                        'workshop_name' => $workshopName,
                        'notes' => $notes,
                        'receipt_image_url' => $receiptImageUrl,
                    ]);
                }
            } else {
                ServiceLog::create([
                    'vehicle_id' => $vehicle->id,
                    'maintenance_item_id' => null,
                    'service_date' => $serviceDate,
                    'odometer_at_service' => $odometerAtService,
                    'actual_cost' => $totalCost,
                    'workshop_name' => $workshopName,
                    'notes' => $notes,
                    'receipt_image_url' => $receiptImageUrl,
                ]);
            }
        }

        // Kalibrasi ulang Odometer jika KM servis lebih besar dari Odo sekarang
        if ($odometerAtService >= $vehicle->current_odometer) {
            $vehicle->update([
                'last_recalibrate_km' => $odometerAtService,
                'last_recalibrate_date' => $serviceDate,
            ]);
        }

        return back()->with('success', 'Catatan servis berhasil ditambahkan ke Paspor Servis!');
    }

    /**
     * Update log servis yang sudah tersimpan (Edit Mode)
     */
    public function updateLog(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'log_ids' => 'required|array',
            'log_ids.*' => 'integer',
            'service_date' => 'required|date',
            'odometer_at_service' => 'required|integer|min:0',
            'workshop_name' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
            'actual_cost' => 'required|numeric|min:0',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $logs = ServiceLog::whereIn('id', $validated['log_ids'])
            ->where('vehicle_id', $vehicle->id)
            ->get();

        if ($logs->isEmpty()) {
            return back()->with('error', 'Data catatan servis tidak ditemukan.');
        }

        $workshopName = $validated['workshop_name'] ?: 'Bengkel Mandiri';
        $serviceDate = $validated['service_date'];
        $odometerAtService = (int) $validated['odometer_at_service'];
        $notes = $validated['notes'] ?? null;
        $totalCost = (float) $validated['actual_cost'];

        // Distribusikan total cost ke entri log
        $count = $logs->count();
        $costEach = $count > 0 ? ($totalCost / $count) : $totalCost;

        foreach ($logs as $log) {
            $log->update([
                'service_date' => $serviceDate,
                'odometer_at_service' => $odometerAtService,
                'workshop_name' => $workshopName,
                'actual_cost' => $costEach,
                'notes' => $notes,
            ]);
        }

        return back()->with('success', 'Catatan servis berhasil diperbarui!');
    }

    /**
     * Hapus entri servis (Bisa 1 nota / grup id)
     */
    public function destroyLog(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'log_ids' => 'required|array',
            'log_ids.*' => 'integer',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        ServiceLog::whereIn('id', $validated['log_ids'])
            ->where('vehicle_id', $vehicle->id)
            ->delete();

        return back()->with('success', 'Catatan servis berhasil dihapus dari Paspor Servis.');
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
            ];
        })->values();

        $totalCost = $groupedLogs->sum('actual_cost');
        $totalServices = $groupedLogs->count();

        return view('pdf.service-passport', compact('vehicle', 'groupedLogs', 'totalCost', 'totalServices'));
    }
}
