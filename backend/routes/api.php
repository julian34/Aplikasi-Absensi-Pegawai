<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanAbsensiController;

Route::middleware(['api'])->group(function () {
    // Public auth routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Absensi routes
        Route::get('/absensi/today', [AbsensiController::class, 'today']);
        Route::post('/absensi/datang', [AbsensiController::class, 'absenDatang']);
        Route::post('/absensi/pulang', [AbsensiController::class, 'absenPulang']);

        // Laporan absensi
        Route::get('/laporan-absensi', [LaporanAbsensiController::class, 'index']);
    });
});