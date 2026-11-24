<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SERVISA
|--------------------------------------------------------------------------
*/

// Landing Page Publik (Bisa diakses tanpa login)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 4 Menu Utama SERVISA (Protected by Auth)
Route::middleware(['auth', 'verified'])->group(function () {
    // Menu 1: Dashboard Servis (Dev 1 - Lead)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/reminders', [DashboardController::class, 'storeOrUpdateReminder'])->name('dashboard.reminders.store');
    Route::post('/dashboard/quick-service', [DashboardController::class, 'quickService'])->name('dashboard.quickService');

    // Menu 2: Kalkulator Kerugian (Dev 3)
    Route::get('/kalkulator', function () {
        return view('pages.calculator');
    })->name('calculator');

    // Menu 3: Diagnosa AI (Dev 2)
    Route::get('/diagnosa', [DiagnosaController::class, 'index'])->name('diagnosa');
    Route::post('/diagnosa/analyze', [DiagnosaController::class, 'analyze'])->name('diagnosa.analyze');
    Route::post('/vehicles/{vehicle}/sync-odometer', [DiagnosaController::class, 'syncOdometer'])->name('vehicles.syncOdometer');

    // Menu 4: Riwayat & Paspor PDF (Dev 4)
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history');
    Route::post('/riwayat/service-logs', [HistoryController::class, 'storeLog'])->name('history.storeLog');
    Route::patch('/riwayat/ai-scans/{aiScan}/resolve', [HistoryController::class, 'resolveAiScan'])->name('history.resolveAiScan');
    Route::get('/vehicles/{vehicle}/passport', [HistoryController::class, 'printPassport'])->name('history.passport');

    // Fitur Kelola Garasi Kendaraan (Index, Switch, Tambah Baru, Update, Kalibrasi, Hapus)
    Route::get('/garasi', [VehicleController::class, 'index'])->name('garage');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::post('/vehicles/{vehicle}/switch', [VehicleController::class, 'switchVehicle'])->name('vehicles.switch');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::put('/vehicles/{vehicle}/recalibrate', [VehicleController::class, 'recalibrate'])->name('vehicles.recalibrate');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // Fitur Profil & Garasi Kendaraan (Breeze Default Extended)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
