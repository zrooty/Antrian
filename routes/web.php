<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/api/status', [\App\Http\Controllers\DashboardController::class, 'status'])->name('dashboard.api.status');

    Route::get('/reservasi', [ReservationController::class, 'create'])->name('reservasi.create');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Petugas (Officer) Logic
    Route::prefix('officer')->name('petugas.')->group(function () {
        Route::get('/', [QueueController::class, 'petugasIndex'])->name('index');
        Route::post('/call', [QueueController::class, 'panggil'])->name('panggil');
        Route::patch('/queue/{queue}/start', [QueueController::class, 'startProcessing'])->name('start');
        Route::patch('/queue/{queue}/finish', [QueueController::class, 'finishQueue'])->name('finish');
        Route::patch('/queue/{queue}/skip', [QueueController::class, 'skipQueue'])->name('skip');
        Route::patch('/queue/{queue}/recall', [QueueController::class, 'recallQueue'])->name('recall');
    });
});

    // Admin Dashboard (Pusat Kontrol)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'rekapIndex'])->name('rekap'); // Keeping legacy name for now
        Route::get('/monitoring', [AdminController::class, 'monitoringIndex'])->name('monitoring');
        Route::get('/monitoring/data', [AdminController::class, 'getMonitoringData'])->name('monitoring.data');
        
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
