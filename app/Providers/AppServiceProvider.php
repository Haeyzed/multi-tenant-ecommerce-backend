<?php

namespace App\Providers;

use App\Contracts\Central\AuthServiceInterface;
use App\Contracts\Central\TenantServiceInterface;
use App\Contracts\Central\UserServiceInterface;
use App\Services\Central\AuthService;
use App\Services\Central\TenantService;
use App\Services\Central\UserService;
use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Auth service binding
        $this->app->singleton(
            AuthServiceInterface::class,
            AuthService::class
        );

        // Tenant service binding
        $this->app->singleton(
            TenantServiceInterface::class,
            TenantService::class
        );

        // User service binding
        $this->app->singleton(
            UserServiceInterface::class,
            UserService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
