<?php

declare(strict_types=1);

namespace App\Providers;

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\ServerVariable;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use App\Support\Tenancy\TenantDomain;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ScrambleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureCentralApiDocs();
        $this->configureTenantApiDocs();
    }

    private function configureCentralApiDocs(): void
    {
        Scramble::configure()
            ->useConfig(array_merge(config('scramble', []), [
                'api_path' => 'api/v1/central',
                'info' => [
                    'version' => config('scramble.info.version', '1.0.0'),
                    'description' => 'Central platform API for tenant lifecycle, billing, and administration.',
                ],
                'ui' => array_merge(config('scramble.ui', []), [
                    'title' => config('app.name').' — Central API',
                ]),
                'servers' => [
                    'Local (central)' => '/api/v1/central',
                ],
            ]))
            ->routes(function (Route $route): bool {
                return Str::startsWith($route->uri, 'api/v1/central')
                    || str_contains($route->getActionName(), 'Api\\Central\\');
            })
            ->expose(
                ui: fn (Router $router, $action) => $router
                    ->get('docs/api', $action)
                    ->name('scramble.docs.ui'),
                document: fn (Router $router, $action) => $router
                    ->get('docs/api.json', $action)
                    ->name('scramble.docs.document'),
            )
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(SecurityScheme::http('bearer'));
            });
    }

    private function configureTenantApiDocs(): void
    {
        $tenantDomain = env('SCRAMBLE_TENANT_DOMAIN', 'peakretail.'.TenantDomain::suffix());
        $scheme = $this->tenantApiScheme();

        Scramble::registerApi('tenant', [
            'api_path' => 'api/v1',
            'info' => [
                'version' => config('scramble.info.version', '1.0.0'),
                'description' => 'Tenant store API. Send requests to the tenant host (see Server variables). '
                    ."Use **{$scheme}** locally (Herd `.test` is usually HTTP unless you enabled SSL). "
                    .'The `tenant_domain` value must match a row in the `domains` table.',
            ],
            'ui' => [
                'title' => config('app.name').' — Tenant API',
                'hide_try_it' => config('scramble.ui.hide_try_it', false),
                'theme' => config('scramble.ui.theme', 'light'),
                'layout' => config('scramble.ui.layout', 'responsive'),
            ],
            'servers' => [
                'Tenant host' => "{$scheme}://{tenant_domain}/api/v1",
            ],
            'middleware' => config('scramble.middleware', ['web', RestrictedDocsAccess::class]),
        ])
            ->routes(function (Route $route): bool {
                if (Str::startsWith($route->uri, 'api/v1/central')) {
                    return false;
                }

                return Str::startsWith($route->uri, 'api/v1')
                    && str_contains($route->getActionName(), 'Api\\Tenant\\');
            })
            ->expose(
                ui: fn (Router $router, $action) => $router
                    ->get('docs/api/tenant', $action)
                    ->name('scramble.docs.tenant.ui'),
                document: fn (Router $router, $action) => $router
                    ->get('docs/api/tenant.json', $action)
                    ->name('scramble.docs.tenant.document'),
            )
            ->withServerVariables([
                'tenant_domain' => ServerVariable::make(
                    default: $tenantDomain,
                    description: 'Full tenant host from `domains.domain` (e.g. peakretail.'.TenantDomain::suffix().')',
                ),
            ])
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(SecurityScheme::http('bearer'));
            });
    }

    /**
     * Local Herd serves .test sites over HTTP unless you explicitly secured the site.
     */
    private function tenantApiScheme(): string
    {
        if ($scheme = env('SCRAMBLE_TENANT_SCHEME')) {
            return rtrim($scheme, '://');
        }

        return str_starts_with((string) config('app.url'), 'https://') ? 'https' : 'http';
    }

}
