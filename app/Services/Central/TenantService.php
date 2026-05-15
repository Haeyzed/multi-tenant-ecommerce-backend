<?php

namespace App\Services\Central;

use App\Contracts\Central\TenantServiceInterface;
use App\DTOs\Central\TenantDTO;
use App\Events\TenantCreated;
use App\Events\TenantSuspended;
use App\Models\Central\Tenant;
use App\Repositories\TenantRepository;
use Artisan;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

readonly class TenantService implements TenantServiceInterface
{
    public function __construct(
        private TenantRepository $repository
    ) {}

    public function getAllTenants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    public function getTenantById(string $id): ?Tenant
    {
        return $this->repository->findById($id);
    }

    /**
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
     * @throws Throwable
     */
    public function updateTenant(string $id, TenantDTO $dto): Tenant
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        return DB::transaction(function () use ($tenant, $dto) {
            return $this->repository->update($tenant, $dto->toArray());
        });
    }

    /**
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
