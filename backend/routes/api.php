<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    // Public auth routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});