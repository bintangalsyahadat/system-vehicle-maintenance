<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
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

        return back()->with('success', 'Beralih ke ' . $vehicle->model_name);
    }
}
