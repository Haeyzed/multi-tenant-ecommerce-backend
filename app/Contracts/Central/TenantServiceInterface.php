<?php

namespace App\Contracts\Central;

use App\DTOs\Central\TenantDTO;
use App\Models\Central\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

interface TenantServiceInterface
{
    /**
     * Get all tenants with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTenants(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get a tenant by its ID.
     *
     * @param string $id
     * @return Tenant|null
     */
    public function getTenantById(string $id): ?Tenant;

    /**
     * Create a new tenant.
     *
     * @param TenantDTO $dto
     * @return Tenant
     */
    public function createTenant(TenantDTO $dto): Tenant;

    /**
     * Update an existing tenant.
     *
     * @param string $id
     * @param TenantDTO $dto
     * @return Tenant
     */
    public function updateTenant(string $id, TenantDTO $dto): Tenant;

    /**
     * Delete a tenant by its ID.
     *
     * @param string $id
     * @return bool
     */
    public function deleteTenant(string $id): bool;

    /**
     * Suspend a tenant by its ID.
     *
     * @param string $id
     * @return Tenant
     */
    public function suspendTenant(string $id): Tenant;

    /**
     * Activate a tenant by its ID.
     *
     * @param string $id
     * @return Tenant
     */
    public function activateTenant(string $id): Tenant;
}
