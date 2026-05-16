<?php

declare(strict_types=1);

namespace Database\Seeders\Central;

use App\Models\Central\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class UserSeeder
 *
 * Seeds default users with roles and permissions for the central platform.
 * Creates super admin, admin, and sample users for testing.
 */
class UserSeeder extends Seeder
{
    private const string DEFAULT_PASSWORD = 'password';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();
        $this->createUsers();

        $this->command->info('Users seeded successfully with roles and permissions.');
    }

    /**
     * Create all system permissions.
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Tenant management
            'tenants.view',
            'tenants.create',
            'tenants.update',
            'tenants.delete',
            'tenants.suspend',
            'tenants.activate',

            // Plan management
            'plans.view',
            'plans.create',
            'plans.update',
            'plans.delete',

            // User management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage_roles',
            'users.manage_permissions',

            // Billing and subscriptions
            'invoices.view',
            'invoices.create',
            'payments.process',
            'subscriptions.manage',

            // Settings
            'settings.view',
            'settings.update',

            // Reports
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'sanctum']
            );
        }

        $this->command->info('Created ' . count($permissions) . ' permissions.');
    }

    /**
     * Create system roles with permission assignments.
     */
    private function createRoles(): void
    {
        // Super Admin - all permissions
        $superAdmin = Role::query()->firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'sanctum']
        );
        $superAdmin->syncPermissions(Permission::all());

        // Admin - most management permissions
        $admin = Role::query()->firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'sanctum']
        );
        $admin->syncPermissions([
            'tenants.view', 'tenants.create', 'tenants.update',
            'plans.view',
            'users.view', 'users.create', 'users.update',
            'invoices.view', 'payments.process',
            'settings.view', 'settings.update',
            'reports.view', 'reports.export',
        ]);

        // Billing Manager - billing focused
        $billingManager = Role::query()->firstOrCreate(
            ['name' => 'billing_manager', 'guard_name' => 'sanctum']
        );
        $billingManager->syncPermissions([
            'invoices.view', 'invoices.create',
            'payments.process',
            'subscriptions.manage',
            'reports.view',
        ]);

        // Support - read-only support access
        $support = Role::query()->firstOrCreate(
            ['name' => 'support', 'guard_name' => 'sanctum']
        );
        $support->syncPermissions([
            'tenants.view',
            'users.view',
            'invoices.view',
        ]);

        // Viewer - minimal read access
        $viewer = Role::query()->firstOrCreate(
            ['name' => 'viewer', 'guard_name' => 'sanctum']
        );
        $viewer->syncPermissions([
            'tenants.view',
            'plans.view',
            'reports.view',
        ]);

        $this->command->info('Created 5 roles with permissions.');
    }

    /**
     * Create default users with assigned roles.
     */
    private function createUsers(): void
    {
        // Super Admin
        $superAdmin = User::query()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@platform.com',
            'phone' => '+234 800 000 0001',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        $this->command->info("Super Admin created: superadmin@platform.com / " . self::DEFAULT_PASSWORD);

        // Admin
        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@platform.com',
            'phone' => '+234 800 000 0002',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $this->command->info("Admin created: admin@platform.com / " . self::DEFAULT_PASSWORD);

        // Billing Manager
        $billingManager = User::query()->create([
            'name' => 'Billing Manager',
            'email' => 'billing@platform.com',
            'phone' => '+234 800 000 0003',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $billingManager->assignRole('billing_manager');

        // Add extra direct permission beyond role
        $billingManager->givePermissionTo('reports.export');

        $this->command->info("Billing Manager created: billing@platform.com / " . self::DEFAULT_PASSWORD);

        // Support Agent
        $supportAgent = User::query()->create([
            'name' => 'Support Agent',
            'email' => 'support@platform.com',
            'phone' => '+234 800 000 0004',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $supportAgent->assignRole('support');

        $this->command->info("Support created: support@platform.com / " . self::DEFAULT_PASSWORD);

        // Inactive User (for testing)
        $inactiveUser = User::query()->create([
            'name' => 'Inactive User',
            'email' => 'inactive@platform.com',
            'phone' => '+234 800 000 0005',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'is_active' => false,
            'email_verified_at' => now(),
        ]);
        $inactiveUser->assignRole('viewer');

        $this->command->info("Inactive User created: inactive@platform.com / " . self::DEFAULT_PASSWORD);

        // Unverified User (for testing email verification)
        $unverifiedUser = User::query()->create([
            'name' => 'Unverified User',
            'email' => 'unverified@platform.com',
            'phone' => '+234 800 000 0006',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'is_active' => true,
            'email_verified_at' => null,
        ]);
        $unverifiedUser->assignRole('viewer');

        $this->command->info("Unverified User created: unverified@platform.com / " . self::DEFAULT_PASSWORD);

        $this->command->newLine();
        $this->command->warn('Default password for all seeded users: ' . self::DEFAULT_PASSWORD);
    }
}
