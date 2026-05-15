<?php

namespace App\Services\Central;

use App\Contracts\Central\TenantServiceInterface;
use App\DTOs\Central\TenantDTO;
use App\Events\Central\TenantCreated;
use App\Events\Central\TenantSuspended;
use App\Models\Central\Tenant;
use App\Repositories\Central\TenantRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

readonly class TenantService implements TenantServiceInterface
{
    /**
     * TenantService constructor.
     *
     * @param TenantRepository $repository
     */
    public function __construct(
        private TenantRepository $repository
    ) {}

    /**
     * Get all tenants with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTenants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Retrieve a tenant by its ID.
     *
     * @param string $id
     * @return Tenant|null
     */
    public function getTenantById(string $id): ?Tenant
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new tenant.
     * 1. Create the tenant
     * 2. Create a domain for the tenant
     * 3. Run migrations for the tenant
     *
     * @throws Throwable
     */
    public function createTenant(TenantDTO $dto): Tenant
    {
        return DB::transaction(function () use ($dto) {
            $tenant = $this->repository->create([
                'id' => Str::uuid()->toString(),
                ...$dto->toArray(),
            ]);

            $tenant->domains()->create([
                'domain' => $dto->domain,
                'is_primary' => true,
            ]);

            $tenant->run(function () {
                Artisan::call('migrate', ['--path' => 'database/migrations/tenant', '--force' => true]);
            });

            event(new TenantCreated($tenant));

            return $tenant->fresh('domains');
        });
    }

    /**
     * Update an existing tenant.
     * 1. Find the tenant by ID
     * 2. Update the tenant details
     * 3. Refresh the tenant with updated data
     * @throws Throwable
     */
    public function updateTenant(string $id, TenantDTO $dto): Tenant
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        return DB::transaction(function () use ($tenant, $dto) {
            $this->repository->update($tenant, $dto->toArray());
            return $tenant->fresh('domains');
        });
    }

    /**
     * Delete a tenant by its ID.
     * 1. Find the tenant by ID
     * 2. Delete the tenant
     *
     * @throws Throwable
     */
    public function deleteTenant(string $id): bool
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        return DB::transaction(function () use ($tenant) {
            return $this->repository->delete($tenant);
        });
    }

    /**
     * Suspend a tenant by its ID.
     * 1. Find the tenant by ID
     * 2. Suspend the tenant
     * 3. Fire the TenantSuspended event
     *
     * @throws Exception
     */
    public function suspendTenant(string $id): Tenant
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        $tenant->suspend();
        event(new TenantSuspended($tenant));

        return $tenant;
    }

    /**
     * Activate a tenant by its ID.
     * 1. Find the tenant by ID
     * 2. Activate the tenant
     *
     * @throws Exception
     */
    public function activateTenant(string $id): Tenant
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        $tenant->activate();

        return $tenant;
    }
}
