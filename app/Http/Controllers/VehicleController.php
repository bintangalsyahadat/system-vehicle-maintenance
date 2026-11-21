<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Tampilkan halaman Garasi Kendaraan milik user
     */
    public function index()
    {
        $vehicles = Auth::user() ? Auth::user()->vehicles : collect();
        return view('pages.garage', compact('vehicles'));
    }

    /**
     * Simpan kendaraan baru milik user yang sedang login
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:100',
            'model_name' => 'required|string|max:100',
            'license_plate' => 'nullable|string|max:20',
            'manufacture_year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'initial_odometer_km' => 'required|integer|min:0',
            'daily_average_km' => 'required|numeric|min:1|max:500',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => Auth::id(),
            'vehicle_type' => 'motor',
            'brand' => $validated['brand'],
            'model_name' => $validated['model_name'],
            'license_plate' => $validated['license_plate'] ?? null,
            'manufacture_year' => $validated['manufacture_year'] ?? null,
            'initial_odometer_km' => $validated['initial_odometer_km'],
            'daily_average_km' => $validated['daily_average_km'],
            'last_recalibrate_km' => $validated['initial_odometer_km'],
            'last_recalibrate_date' => now()->toDateString(),
        ]);

        // Otomatis jadikan kendaraan yang baru dibuat sebagai kendaraan aktif
        session(['active_vehicle_id' => $vehicle->id]);

        return back()->with('success', 'Kendaraan ' . $vehicle->model_name . ' berhasil ditambahkan!');
    }

    /**
     * Ganti kendaraan aktif yang dipilih oleh user
     */
    public function switchVehicle(Vehicle $vehicle)
    {
        // Pastikan hanya bisa memilih kendaraan miliknya sendiri
        if ($vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        session(['active_vehicle_id' => $vehicle->id]);

        return redirect()->route('dashboard')->with('success', 'Beralih ke kendaraan ' . $vehicle->model_name);
    }

    /**
     * Update data spesifikasi kendaraan milik user (Merk, Model, Plat, Tahun)
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'brand' => 'required|string|max:100',
            'model_name' => 'required|string|max:100',
            'license_plate' => 'nullable|string|max:20',
            'manufacture_year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
        ]);

        $vehicle->update([
            'brand' => $validated['brand'],
            'model_name' => $validated['model_name'],
            'license_plate' => $validated['license_plate'] ?? null,
            'manufacture_year' => $validated['manufacture_year'] ?? null,
        ]);

        return back()->with('success', 'Data kendaraan ' . $vehicle->model_name . ' berhasil diperbarui!');
    }

    /**
     * Kalibrasi ulang Odometer dan/atau Rata-rata KM harian kendaraan
     */
    public function recalibrate(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'current_odometer_km' => 'required|integer|min:0',
            'daily_average_km' => 'required|numeric|min:1|max:500',
        ]);

        $vehicle->update([
            'last_recalibrate_km' => $validated['current_odometer_km'],
            'last_recalibrate_date' => now()->toDateString(),
            'daily_average_km' => $validated['daily_average_km'],
        ]);

        return back()->with('success', 'Kalibrasi odometer ' . $vehicle->model_name . ' berhasil disimpan!');
    }

    /**
     * Hapus kendaraan milik user
     */
    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika yang dihapus adalah kendaraan aktif, reset session
        if (session('active_vehicle_id') == $vehicle->id) {
            session()->forget('active_vehicle_id');
        }

        $vehicle->delete();

        return back()->with('success', 'Kendaraan berhasil dihapus dari garasi.');
    }
}
