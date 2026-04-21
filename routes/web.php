<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'admin') {
        return redirect()->route('admin.rekap');
    } elseif ($role === 'petugas') {
        return redirect()->route('petugas.antrian');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/reservasi', [ReservationController::class, 'create'])->name('reservasi.create');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Queue Calling Routes (Petugas)
    Route::get('/petugas/antrian', [\App\Http\Controllers\QueueController::class, 'petugasIndex'])->name('petugas.antrian');
    Route::post('/petugas/antrian/{queue}/panggil', [\App\Http\Controllers\QueueController::class, 'panggil'])->name('petugas.panggil');
});

    // Admin Dashboard (Pusat Kontrol)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'rekapIndex'])->name('rekap'); // Keeping legacy name for now
        Route::get('/monitoring', [AdminController::class, 'monitoringIndex'])->name('monitoring');
        
        // Manajemen Data
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
        Route::resource('counters', \App\Http\Controllers\Admin\CounterController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Aksi & Operasional
        Route::get('/operational', [AdminController::class, 'operationalIndex'])->name('operational');
        Route::post('/operational/reset', [AdminController::class, 'resetQueue'])->name('operational.reset');
        
        // Laporan & Analisis
        Route::get('/dashboard/data', [AdminController::class, 'getData'])->name('rekap.data');
        Route::get('/reports', [AdminController::class, 'reportIndex'])->name('reports');
        Route::get('/reports/data', [AdminController::class, 'getReportData'])->name('reports.data');
        
        // Konfigurasi & Log
        Route::get('/settings', [AdminController::class, 'settingIndex'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/logs', [AdminController::class, 'logIndex'])->name('logs');
    });

// TV Display (Public)
Route::get('/tv', [\App\Http\Controllers\QueueController::class, 'tvIndex'])->name('tv.index');

require __DIR__.'/auth.php';
