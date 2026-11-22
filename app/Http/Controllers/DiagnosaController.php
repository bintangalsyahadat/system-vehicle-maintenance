<?php

namespace App\Http\Controllers;

use App\Models\AiScanHistory;
use App\Models\Vehicle;
use App\Services\GeminiVisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DiagnosaController extends Controller
{
    protected GeminiVisionService $geminiService;

    public function __construct(GeminiVisionService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Tampilkan halaman Diagnosa AI
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vehicles = $user ? $user->vehicles : collect();

        $activeVehicleId = session('active_vehicle_id');
        $activeVehicle = $vehicles->firstWhere('id', $activeVehicleId) ?: $vehicles->first();

        $scanHistories = collect();
        if ($activeVehicle) {
            $scanHistories = AiScanHistory::where('vehicle_id', $activeVehicle->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        return view('pages.diagnosa', compact('vehicles', 'activeVehicle', 'scanHistories'));
    }

    /**
     * Analisis foto speedometer dengan Gemini AI Vision
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'image_file' => 'nullable|image|max:10240', // max 10MB
            'image_base64' => 'nullable|string', // Support direct camera stream capture
        ]);

        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $rawBase64 = null;
        $mimeType = 'image/jpeg';
        $savedPath = null;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $mimeType = $file->getMimeType();
            $rawBase64 = base64_encode(file_get_contents($file->getRealPath()));
            $savedPath = $file->store('scans', 'public');
        } elseif ($request->filled('image_base64')) {
            $dataUri = $request->image_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUri, $matches)) {
                $mimeType = 'image/' . $matches[1];
                $rawBase64 = substr($dataUri, strpos($dataUri, ',') + 1);
                
                // Simpan ke storage public
                $imageBinary = base64_decode($rawBase64);
                $filename = 'scans/' . uniqid() . '.' . ($matches[1] === 'jpeg' ? 'jpg' : $matches[1]);
                Storage::disk('public')->put($filename, $imageBinary);
                $savedPath = $filename;
            }
        }

        if (!$rawBase64) {
            return back()->with('error', 'Silakan pilih foto atau ambil gambar dari kamera terlebih dahulu.');
        }

        // Panggil Gemini AI Vision Service dengan konteks kendaraan aktif
        $analysis = $this->geminiService->analyzeSpeedometer($rawBase64, $mimeType, $vehicle);

        // Jika AI mendeteksi gambar BUKAN speedometer (Guardrail out-of-scope)
        if (isset($analysis['is_valid_speedometer']) && !$analysis['is_valid_speedometer']) {
            return back()->with('error', $analysis['rejection_reason'] ?: 'Gambar terdeteksi bukan speedometer kendaraan bermotor. Harap foto instrumen speedometer yang jelas.');
        }

        // Simpan ke riwayat scan AI di database
        $scan = AiScanHistory::create([
            'vehicle_id' => $vehicle->id,
            'image_url' => $savedPath ? Storage::url($savedPath) : null,
            'detected_indicator' => $analysis['detected_indicator'] ?? 'Indikator Normal',
            'severity_level' => $analysis['severity_level'] ?? 'INFO',
            'emergency_action' => $analysis['emergency_action'] ?? 'Lakukan perawatan berkala secara rutin.',
            'estimated_cost_min' => $analysis['estimated_cost_min'] ?? 0,
            'estimated_cost_max' => $analysis['estimated_cost_max'] ?? 0,
            'is_resolved' => false,
        ]);

        return back()->with([
            'success' => 'Pemindaian speedometer berhasil dianalisis oleh AI!',
            'latest_scan_id' => $scan->id,
            'detected_odometer' => $analysis['detected_odometer'] ?? null,
        ]);
    }

    /**
     * Sinkronkan angka odometer hasil scan AI ke kendaraan
     */
    public function syncOdometer(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'odometer' => 'required|integer|min:0',
        ]);

        $vehicle->update([
            'last_recalibrate_km' => $validated['odometer'],
            'last_recalibrate_date' => now()->toDateString(),
        ]);

        return back()->with('success', "Odometer {$vehicle->model_name} berhasil disinkronkan ke {$validated['odometer']} KM!");
    }
}
