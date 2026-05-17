<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Central\TenantStatus;
use App\Models\Central\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Block API access when the central tenant record is suspended or inactive.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();

        if ($tenant === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant context could not be resolved.',
            ], 404);
        }

        if ($tenant->isSuspended()) {
            return response()->json([
                'success' => false,
                'message' => 'This store has been suspended. Please contact platform support.',
            ], 403);
        }

        if ($tenant->status === TenantStatus::INACTIVE) {
            return response()->json([
                'success' => false,
                'message' => 'This store is not active.',
            ], 403);
        }

        return $next($request);
    }
}
