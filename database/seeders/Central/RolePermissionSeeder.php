<?php

declare(strict_types=1);

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class RolePermissionSeeder
 *
 * Seeds default roles and permissions for the platform.
 *
 * @package Database\Seeders
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
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

            // Billing
            'invoices.view',
            'invoices.create',
            'payments.process',
            'subscriptions.manage',

            // Settings
            'settings.view',
            'settings.update',

            // Reports
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Create roles with permissions
        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'sanctum']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->givePermissionTo([
            'tenants.view', 'tenants.create', 'tenants.update',
            'plans.view',
            'users.view', 'users.create', 'users.update',
            'invoices.view', 'payments.process',
            'settings.view', 'settings.update',
            'reports.view',
        ]);

        $billingManager = Role::create(['name' => 'billing_manager', 'guard_name' => 'sanctum']);
        $billingManager->givePermissionTo([
            'invoices.view', 'invoices.create',
            'payments.process',
            'subscriptions.manage',
            'reports.view',
        ]);

        $support = Role::create(['name' => 'support', 'guard_name' => 'sanctum']);
        $support->givePermissionTo([
            'tenants.view',
            'users.view',
            'invoices.view',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
