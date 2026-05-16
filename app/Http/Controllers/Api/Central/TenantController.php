<?php

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\TenantServiceInterface;
use App\DTOs\Central\TenantDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreateTenantRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Http\Resources\Central\TenantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(
        private readonly TenantServiceInterface $tenantService
    ) {}

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

    public function destroy(string $id): JsonResponse
    {
        $this->tenantService->deleteTenant($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully',
        ]);
    }

    public function suspend(string $id): JsonResponse
    {
        $tenant = $this->tenantService->suspendTenant($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant suspended successfully',
            'data' => new TenantResource($tenant),
        ]);
    }

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
