<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Contracts\Central\TenantServiceInterface;
use App\DTOs\Central\TenantDTO;
use App\Events\Central\TenantCreated;
use App\Events\Central\TenantSuspended;
use App\Models\Central\Tenant;
use App\Models\Tenant\User;
use App\Repositories\Central\TenantRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        private TenantRepository $repository
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
     * Steps:
     * 1. Create a tenant record in a central database
     * 2. Create a primary domain for the tenant
     * 3. Run tenant migrations and seed default settings
     * 4. Generate or use the provided admin password
     * 5. Create a tenant admin user and assign a role
     * 6. Fire TenantCreated event to send welcome emails
     *
     * @param TenantDTO $dto Data transfer object containing tenant and admin details
     * @return Tenant The created tenant with loaded domains
     * @throws Throwable When database transaction fails
     */
    public function createTenant(TenantDTO $dto): Tenant
    {
        return DB::transaction(function () use ($dto) {
            // 1. Create tenant
            $tenant = $this->repository->create([
                'id' => Str::uuid()->toString(),
                ...$dto->toArray(),
            ]);

            // 2. Create primary domain
            $tenant->domains()->create([
                'domain' => $dto->domain,
                'is_primary' => true,
            ]);

            // 3. Run tenant migrations and seed default settings
            $tenant->run(function () {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\Tenant\\SettingSeeder',
                    '--force' => true,
                ]);
            });

            // 4. Generate or use the provided password
            $plainPassword = $dto->adminPassword ?? $this->generateSecurePassword();

            // 5. Create tenant admin user
            $tenant->run(function () use ($tenant, $dto, $plainPassword) {
                $user = User::query()->create([
                    'name' => $dto->adminName,
                    'email' => $dto->adminEmail,
                    'password' => Hash::make($plainPassword),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('admin');

                // 6. Fire event with credentials for email notification
                event(new TenantCreated($tenant, $user, $plainPassword));
            });

            return $tenant->fresh(['domains']);
        });
    }

    /**
     * Update an existing tenant.
     *
     * @param string $id The tenant UUID
     * @param TenantDTO $dto Updated tenant data
     * @return Tenant The updated tenant instance
     * @throws Exception When tenant is not found
     * @throws Throwable When database transaction fails
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
     *
     * @param string $id The tenant UUID
     * @return bool True if deletion was successful
     * @throws Exception When tenant is not found
     * @throws Throwable When database transaction fails
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
     *
     * Prevents tenant access while preserving all data.
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
     *
     * Creates a 12-character password with a mixed case, numbers, and symbols.
     *
     * @return string The generated password
     */
    private function generateSecurePassword(): string
    {
        return Str::random(12);
    }
}
