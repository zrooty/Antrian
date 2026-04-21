<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
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

    // Admin Reports
    Route::get('/admin/rekap', [\App\Http\Controllers\AdminController::class, 'rekapIndex'])->name('admin.rekap');
    Route::get('/admin/rekap/data', [\App\Http\Controllers\AdminController::class, 'getData'])->name('admin.rekap.data');
});

// TV Display (Public)
Route::get('/tv', [\App\Http\Controllers\QueueController::class, 'tvIndex'])->name('tv.index');

require __DIR__.'/auth.php';
