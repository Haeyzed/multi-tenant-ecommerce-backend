<?php

namespace App\Repositories;

use App\Models\Central\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TenantRepository
{
    /**
     * Get all tenants with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Tenant::class)
            ->allowedFilters(
                AllowedFilter::exact('status'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                'plan',
            )
            ->allowedSorts('name', 'created_at', 'status')
            ->with('domains')
            ->paginate($perPage);
    }

    /**
     * Find a tenant by ID with associated domains.
     *
     * @param string $id
     * @return Tenant|null
     */
    public function findById(string $id): ?Tenant
    {
        return Tenant::with('domains')->find($id);
    }

    /**
     * Create a new tenant.
     *
     * @param array $data
     * @return Tenant
     */
    public function create(array $data): Tenant
    {
        return Tenant::query()->create($data);
    }

    /**
     * Update an existing tenant.
     *
     * @param Tenant $tenant
     * @param array $data
     * @return Tenant
     */
    public function update(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);
        return $tenant->fresh();
    }

    /**
     * Delete an existing tenant.
     *
     * @param Tenant $tenant
     * @return bool
     */
    public function delete(Tenant $tenant): bool
    {
        return $tenant->delete();
    }
}
