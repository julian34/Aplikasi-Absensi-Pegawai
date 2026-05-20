<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    // Public auth routes
    Route::post('/login', [AuthController::class, 'login']);
    
    // Handle OPTIONS requests for CORS preflight
    Route::options('{any}', function (Request $request) {
        return response()
            ->header('Access-Control-Allow-Origin', $request->header('Origin') ?? '*')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '86400');
    })->where('any', '.*');

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

