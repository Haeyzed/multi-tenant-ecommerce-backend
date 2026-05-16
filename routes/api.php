<?php

use App\Http\Controllers\Api\Central\AuthController;
use App\Http\Controllers\Api\Central\PlanController;
use App\Http\Controllers\Api\Central\TenantController;
use App\Http\Controllers\Api\Central\UserController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::prefix('v1/central/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes
Route::prefix('v1/central')->middleware(['auth:sanctum'])->group(function () {

    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Users
    // User management with permissions
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
    Route::post('users/{user}/assign-permissions', [UserController::class, 'assignPermissions']);
    Route::post('users/{user}/revoke-permissions', [UserController::class, 'revokePermissions']);
    Route::post('users/{user}/sync-permissions', [UserController::class, 'syncPermissions']);
    Route::get('users/{user}/permissions', [UserController::class, 'permissions']);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);

    // Tenants
    Route::apiResource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend']);
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate']);

    // Plans
    Route::apiResource('plans', PlanController::class);
    Route::get('plans-dropdown', [PlanController::class, 'dropdown']);
});
