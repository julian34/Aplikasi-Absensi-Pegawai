<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    // Handle preflight requests (CORS middleware handles headers)
    Route::options('/{any}', function () {
        return response()->json([]);
    })->where('any', '.*');
    
    // Sanctum CSRF Cookie endpoint
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });
    
    // Public auth routes
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});