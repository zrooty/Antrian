<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OfficerController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\UserController;

Route::middleware(['web', 'auth'])->group(function () {
    // User Stats
    Route::get('/user/status', [UserController::class, 'status'])->name('api.user.status');

    // Officer Dashboard & Actions
    Route::prefix('officer')->name('api.officer.')->group(function () {
        Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
        Route::post('/call', [OfficerController::class, 'call'])->name('call');
        Route::patch('/queue/{queue}/start', [OfficerController::class, 'start'])->name('start');
        Route::patch('/queue/{queue}/finish', [OfficerController::class, 'finish'])->name('finish');
        Route::patch('/queue/{queue}/skip', [OfficerController::class, 'skip'])->name('skip');
        Route::patch('/queue/{queue}/recall', [OfficerController::class, 'recall'])->name('recall');
    });

    // Admin Data & Monitoring
    Route::middleware('role:admin')->prefix('admin')->name('api.admin.')->group(function () {
        Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });
});
