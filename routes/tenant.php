<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Tenant\AuthController;
use App\Http\Controllers\Api\Tenant\SettingController;
use App\Http\Controllers\Api\Tenant\UserController;
use App\Http\Middleware\EnsureTenantIsActive;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant API Routes
|--------------------------------------------------------------------------
|
| Loaded on tenant domains only (see TenancyServiceProvider).
| Example host: greenmart.localhost
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    EnsureTenantIsActive::class,
])->prefix('api/v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('resend-verification-otp', [AuthController::class, 'resendVerificationOtp']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
            Route::post('change-password', [AuthController::class, 'changePassword']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);

        Route::get('settings', [SettingController::class, 'index']);
        Route::get('settings/branding', [SettingController::class, 'branding']);
        Route::put('settings', [SettingController::class, 'update']);
        Route::put('settings/branding', [SettingController::class, 'updateBranding']);
        Route::put('settings/payments', [SettingController::class, 'updatePayments']);
        Route::post('settings/toggle-status', [SettingController::class, 'toggleStatus']);
    });
});
