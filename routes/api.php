<?php

use App\Http\Controllers\Api\Central\TenantController;
use App\Http\Controllers\Api\Tenant\AuthController;
use App\Http\Controllers\Api\Tenant\UserController;
use Illuminate\Support\Facades\Route;

// Central domain routes (tenant management)
Route::prefix('v1/central')->middleware(['api'])->group(function () {
    Route::apiResource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend']);
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate']);
});

// Tenant domain routes
Route::prefix('v1')->middleware(['api', 'tenant'])->group(function () {
    // Public routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // User management
        Route::apiResource('users', UserController::class);
    });
});
