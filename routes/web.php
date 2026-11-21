<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SERVISA
|--------------------------------------------------------------------------
*/

Route::get('/');

// 4 Menu Utama SERVISA (Protected by Auth)
Route::middleware(['auth', 'verified'])->group(function () {
    // Menu 1: Dashboard Servis (Dev 1 - Lead)
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Menu 2: Kalkulator Kerugian (Dev 3)
    Route::get('/kalkulator', function () {
        return view('pages.calculator');
    })->name('calculator');

    // Menu 3: Diagnosa AI (Dev 2)
    Route::get('/diagnosa', function () {
        return view('pages.diagnosa');
    })->name('diagnosa');

    // Menu 4: Riwayat & Paspor PDF (Dev 4)
    Route::get('/riwayat', function () {
        return view('pages.history');
    })->name('history');

    // Fitur Kelola Kendaraan (Switch & Tambah Baru)
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::post('/vehicles/{vehicle}/switch', [VehicleController::class, 'switchVehicle'])->name('vehicles.switch');

    // Fitur Profil & Garasi Kendaraan (Breeze Default Extended)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
