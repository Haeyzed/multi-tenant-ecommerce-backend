<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\TenantServiceInterface;
use App\DTOs\Central\TenantDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreateTenantRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Http\Resources\Central\TenantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TenantController
 *
 * Handles API endpoints for central tenant management.
 * Provides CRUD operations, suspension, and activation
 * for multi-tenant platform administration.
 *
 * @package App\Http\Controllers\Api\Central
 */
class TenantController extends Controller
{
    /**
     * TenantController constructor.
     *
     * @param TenantServiceInterface $tenantService The tenant service instance
     */
    public function __construct(
        private readonly TenantServiceInterface $tenantService
    ) {}

    /**
     * Display a listing of all tenants.
     *
     * Supports filtering, sorting, and pagination via query parameters.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse Paginated list of tenants
     */
    public function index(Request $request): JsonResponse
    {
        $tenants = $this->tenantService->getAllTenants(
            $request->all(),
            $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => TenantResource::collection($tenants),
            'meta' => [
                'current_page' => $tenants->currentPage(),
                'per_page' => $tenants->perPage(),
                'total' => $tenants->total(),
            ],
        ]);
    }

    /**
     * Store a newly created tenant.
     *
     * Creates tenant record, provisions database, runs migrations,
     * seeds default settings, creates admin user, and sends welcome emails.
     *
     * @param CreateTenantRequest $request Validated tenant creation data
     * @return JsonResponse The created tenant resource (HTTP 201)
     */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantService->createTenant(
            TenantDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully',
            'data' => new TenantResource($tenant),
        ], 201);
    }

    /**
     * Display the specified tenant.
     *
     * @param string $id The tenant UUID
     * @return JsonResponse The tenant resource or 404 error
     */
    public function show(string $id): JsonResponse
    {
        $tenant = $this->tenantService->getTenantById($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TenantResource($tenant),
        ]);
    }

    /**
     * Update the specified tenant.
     *
     * @param UpdateTenantRequest $request Validated tenant update data
     * @param string $id The tenant UUID
     * @return JsonResponse The updated tenant resource
     */
    public function update(UpdateTenantRequest $request, string $id): JsonResponse
    {
        $tenant = $this->tenantService->updateTenant(
            $id,
            TenantDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => 'Tenant updated successfully',
            'data' => new TenantResource($tenant),
        ]);
    }

    /**
     * Remove the specified tenant.
     *
     * Permanently deletes tenant and associated data.
     *
     * @param string $id The tenant UUID
     * @return JsonResponse Success confirmation
     */
    public function destroy(string $id): JsonResponse
    {
        $this->tenantService->deleteTenant($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully',
        ]);
    }

    /**
     * Suspend the specified tenant.
     *
     * Prevents tenant access while preserving all data.
     *
     * @param string $id The tenant UUID
     * @return JsonResponse The suspended tenant resource
     */
    public function suspend(string $id): JsonResponse
    {
        $tenant = $this->tenantService->suspendTenant($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant suspended successfully',
            'data' => new TenantResource($tenant),
        ]);
    }

    /**
     * Activate a previously suspended tenant.
     *
     * Restores full tenant access.
     *
     * @param string $id The tenant UUID
     * @return JsonResponse The activated tenant resource
     */
    public function activate(string $id): JsonResponse
    {
        $tenant = $this->tenantService->activateTenant($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant activated successfully',
            'data' => new TenantResource($tenant),
        ]);
    }
}
