<?php

namespace App\Providers;

use App\Contracts\Central\AuthServiceInterface;
use App\Contracts\Central\InvoiceServiceInterface;
use App\Contracts\Central\PaymentServiceInterface;
use App\Contracts\Central\PlanServiceInterface;
use App\Contracts\Central\SettingServiceInterface as CentralSettingServiceInterface;
use App\Contracts\Central\SubscriptionServiceInterface;
use App\Contracts\Central\TenantServiceInterface;
use App\Contracts\Central\UserServiceInterface;
use App\Contracts\Tenant\AuthServiceInterface as TenantAuthServiceInterface;
use App\Contracts\Tenant\SettingServiceInterface as TenantSettingServiceInterface;
use App\Contracts\Tenant\UserServiceInterface as TenantUserServiceInterface;
use App\Services\Central\AuthService;
use App\Services\Central\InvoiceService;
use App\Services\Central\PaymentService;
use App\Services\Central\PlanService;
use App\Services\Central\SettingService as CentralSettingService;
use App\Services\Central\SubscriptionService;
use App\Services\Central\TenantService;
use App\Services\Central\UserService;
use App\Services\Tenant\AuthService as TenantAuthService;
use App\Services\Tenant\SettingService as TenantSettingService;
use App\Services\Tenant\UserService as TenantUserService;
use App\Support\Notifications\CentralNotificationTemplateCatalog;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(TenantServiceInterface::class, TenantService::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(PlanServiceInterface::class, PlanService::class);
        $this->app->singleton(SubscriptionServiceInterface::class, SubscriptionService::class);
        $this->app->singleton(CentralSettingServiceInterface::class, CentralSettingService::class);
        $this->app->singleton(InvoiceServiceInterface::class, InvoiceService::class);
        $this->app->singleton(PaymentServiceInterface::class, PaymentService::class);

        $this->app->singleton(TenantAuthServiceInterface::class, TenantAuthService::class);
        $this->app->singleton(TenantUserServiceInterface::class, TenantUserService::class);
        $this->app->singleton(TenantSettingServiceInterface::class, TenantSettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CentralNotificationTemplateCatalog::syncIfEmpty();
    }
}
