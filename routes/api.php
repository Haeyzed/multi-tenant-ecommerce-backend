<?php

use App\Http\Controllers\Api\Central\AuthController;
use App\Http\Controllers\Api\Central\PlanController;
use App\Http\Controllers\Api\Central\PlanModuleController;
use App\Http\Controllers\Api\Central\SettingController;
use App\Http\Controllers\Api\Central\SubscriptionController;
use App\Http\Controllers\Api\Central\TenantController;
use App\Http\Controllers\Api\Central\UserController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::prefix('v1/central/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-verification-otp', [AuthController::class, 'resendVerificationOtp']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes
Route::prefix('v1/central')->middleware(['auth:sanctum'])->group(function () {

    // Auth (any authenticated user)
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Users
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('central.users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('central.users.show');
        Route::get('users/{user}/permissions', [UserController::class, 'permissions']);
    });

    Route::middleware('permission:users.create')->post('users', [UserController::class, 'store'])->name('central.users.store');

    Route::middleware('permission:users.update')->group(function () {
        Route::put('users/{user}', [UserController::class, 'update'])->name('central.users.update');
        Route::patch('users/{user}', [UserController::class, 'update']);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    });

    Route::middleware('permission:users.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('central.users.destroy');

    Route::middleware('permission:users.manage_roles')->post('users/{user}/assign-role', [UserController::class, 'assignRole']);

    Route::middleware('permission:users.manage_permissions')->group(function () {
        Route::post('users/{user}/assign-permissions', [UserController::class, 'assignPermissions']);
        Route::post('users/{user}/revoke-permissions', [UserController::class, 'revokePermissions']);
        Route::post('users/{user}/sync-permissions', [UserController::class, 'syncPermissions']);
    });

    // Tenants
    Route::middleware('permission:tenants.view')->group(function () {
        Route::get('tenants', [TenantController::class, 'index'])->name('central.tenants.index');
        Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('central.tenants.show');
    });

    Route::middleware('permission:tenants.create')->post('tenants', [TenantController::class, 'store'])->name('central.tenants.store');

    Route::middleware('permission:tenants.update')->group(function () {
        Route::put('tenants/{tenant}', [TenantController::class, 'update'])->name('central.tenants.update');
        Route::patch('tenants/{tenant}', [TenantController::class, 'update']);
    });

    Route::middleware('permission:tenants.delete')->delete('tenants/{tenant}', [TenantController::class, 'destroy'])->name('central.tenants.destroy');

    Route::middleware('permission:tenants.suspend')->post('tenants/{tenant}/suspend', [TenantController::class, 'suspend']);
    Route::middleware('permission:tenants.activate')->post('tenants/{tenant}/activate', [TenantController::class, 'activate']);

    // Subscriptions
    Route::middleware('permission:subscriptions.manage')->group(function () {
        Route::get('subscriptions', [SubscriptionController::class, 'index']);
        Route::post('tenants/{tenant}/subscriptions', [SubscriptionController::class, 'store']);
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
        Route::get('tenants/{tenant}/subscription-status', [SubscriptionController::class, 'checkStatus']);
    });

    // Plans & modules (static paths before {plan})
    Route::middleware('permission:plans.view')->group(function () {
        Route::get('plans-dropdown', [PlanController::class, 'dropdown']);
        Route::get('plan-modules', [PlanModuleController::class, 'index']);
        Route::get('plans', [PlanController::class, 'index'])->name('central.plans.index');
        Route::get('plans/{plan}', [PlanController::class, 'show'])->name('central.plans.show');
    });

    Route::middleware('permission:plans.create')->post('plans', [PlanController::class, 'store'])->name('central.plans.store');

    Route::middleware('permission:plans.update')->group(function () {
        Route::put('plans/{plan}', [PlanController::class, 'update'])->name('central.plans.update');
        Route::patch('plans/{plan}', [PlanController::class, 'update']);
    });

    Route::middleware('permission:plans.delete')->delete('plans/{plan}', [PlanController::class, 'destroy'])->name('central.plans.destroy');

    // Platform settings
    Route::middleware('permission:settings.view')->get('settings', [SettingController::class, 'show']);
    Route::middleware('permission:settings.update')->group(function () {
        // Route::put('settings', [SettingController::class, 'update']);
        Route::patch('settings', [SettingController::class, 'update']);
        Route::post('settings/toggle-maintenance', [SettingController::class, 'toggleMaintenance']);
    });
});