<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Contracts\Central\TenantServiceInterface;
use App\DTOs\Central\TenantDTO;
use App\DTOs\Central\UpdateTenantDTO;
use App\Events\Central\TenantCreated;
use App\Events\Central\TenantSuspended;
use App\Models\Central\Tenant;
use App\Models\Tenant\Setting;
use App\Models\Tenant\User;
use App\Repositories\Central\TenantRepository;
use App\Services\Central\LocalDevelopment\TenantHostRegistrar;
use App\Support\Tenancy\TenantDomain;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class TenantService
 *
 * Handles tenant lifecycle management including creation, updates,
 * suspension, and activation. Automatically provisions tenant databases,
 * runs migrations, seeds default settings, and creates an admin user.
 *
 * @package App\Services\Central
 */
readonly class TenantService implements TenantServiceInterface
{
    /**
     * TenantService constructor.
     *
     * @param TenantRepository $repository Repository for tenant data access
     */
    public function __construct(
        private TenantRepository $repository,
        private TenantHostRegistrar $tenantHostRegistrar,
    ) {}

    /**
     * Get all tenants with optional filters and pagination.
     *
     * @param array $filters Query filters for tenant listing
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator Paginated tenant collection
     */
    public function getAllTenants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Retrieve a tenant by its ID.
     *
     * @param string $id The tenant UUID
     * @return Tenant|null The tenant instance or null if not found
     */
    public function getTenantById(string $id): ?Tenant
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new tenant with an admin user.
     *
     * @param TenantDTO $dto Data transfer object containing tenant and admin details
     * @return Tenant The created tenant with loaded domains
     * @throws Throwable When provisioning fails
     */
    public function createTenant(TenantDTO $dto): Tenant
    {
        $tenant = null;
        $adminUser = null;
        $plainPassword = $dto->adminPassword ?? $this->generateSecurePassword();

        try {
            $tenant = $this->repository->create([
                'id' => Str::uuid()->toString(),
                ...$dto->toArray(),
            ]);

            $tenantHost = TenantDomain::qualify($dto->domain);

            $tenant->domains()->create([
                'domain' => $tenantHost,
                'is_primary' => true,
            ]);

            $tenant->run(function () use ($dto, $plainPassword, &$adminUser) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\Tenant\\TenantDatabaseSeeder',
                    '--force' => true,
                ]);

                if ($dto->storeSettings !== null) {
                    Setting::updateSettings($dto->storeSettings->toArray());
                }

                $adminUser = User::query()->create([
                    'name' => $dto->adminName,
                    'email' => $dto->adminEmail,
                    'password' => $plainPassword,
                    'email_verified_at' => now(),
                ]);

                $adminUser->assignRole('super_admin');
            });

            if ($adminUser === null) {
                throw new Exception('Failed to create tenant admin user.');
            }

            event(new TenantCreated($tenant, $adminUser, $plainPassword));

            $this->tenantHostRegistrar->register($tenantHost);

            return $tenant->fresh(['domains', 'plan']);
        } catch (Throwable $e) {
            $tenant?->forceDelete();

            throw $e;
        }
    }

    /**
     * Update an existing tenant.
     *
     * @param string $id The tenant UUID
     * @param UpdateTenantDTO $dto Updated tenant data
     * @return Tenant The updated tenant instance
     * @throws Exception When tenant is not found
     * @throws Throwable When database transaction fails
     */
    public function updateTenant(string $id, UpdateTenantDTO $dto): Tenant
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        return DB::transaction(function () use ($tenant, $dto) {
            $attributes = $dto->toArray();

            if ($attributes !== []) {
                $this->repository->update($tenant, $attributes);
            }

            if ($dto->hasDomain()) {
                $primaryDomain = $tenant->domains()->where('is_primary', true)->first()
                    ?? $tenant->domains()->first();

                $tenantHost = TenantDomain::qualify($dto->domain);

                if ($primaryDomain) {
                    $oldHost = $primaryDomain->domain;
                    $primaryDomain->update(['domain' => $tenantHost]);

                    if ($oldHost !== $tenantHost) {
                        $this->tenantHostRegistrar->unregister($oldHost);
                        $this->tenantHostRegistrar->register($tenantHost);
                    }
                } else {
                    $tenant->domains()->create([
                        'domain' => $tenantHost,
                        'is_primary' => true,
                    ]);

                    $this->tenantHostRegistrar->register($tenantHost);
                }
            }

            return $tenant->fresh(['domains', 'plan']);
        });
    }

    /**
     * Delete a tenant by its ID (including tenant database).
     *
     * @param string $id The tenant UUID
     * @return bool True if deletion was successful
     * @throws Exception When tenant is not found
     */
    public function deleteTenant(string $id): bool
    {
        $tenant = $this->repository->findById($id);

        if (!$tenant) {
            throw new Exception('Tenant not found');
        }

        $tenant->load('domains');

        foreach ($tenant->domains as $domain) {
            $this->tenantHostRegistrar->unregister($domain->domain);
        }

        return $this->repository->delete($tenant);
    }

    /**
     * Suspend a tenant by its ID.
     *
     * @param string $id The tenant UUID
     * @return Tenant The suspended tenant instance
     * @throws Exception When tenant is not found
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
     * Activate a previously suspended tenant.
     *
     * @param string $id The tenant UUID
     * @return Tenant The activated tenant instance
     * @throws Exception When tenant is not found
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

    /**
     * Generate a cryptographically secure random password.
     */
    private function generateSecurePassword(): string
    {
        return Str::random(12);
    }
}
