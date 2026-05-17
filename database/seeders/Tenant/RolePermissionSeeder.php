<?php

declare(strict_types=1);

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds default roles and permissions for the tenant store application.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();

        $this->command->info('Tenant roles and permissions seeded successfully.');
    }

    private function createPermissions(): void
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage_roles',
            'users.manage_permissions',
            'settings.view',
            'settings.update',
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(
                ['name' => $permission, 'guard_name' => 'sanctum']
            );
        }

        $this->command->info('Created '.count($permissions).' permissions.');
    }

    private function createRoles(): void
    {
        $superAdmin = Role::query()->firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'sanctum']
        );
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::query()->firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'sanctum']
        );
        $admin->syncPermissions([
            'users.view', 'users.create', 'users.update',
            'settings.view', 'settings.update',
            'reports.view',
        ]);

        $billingManager = Role::query()->firstOrCreate(
            ['name' => 'billing_manager', 'guard_name' => 'sanctum']
        );
        $billingManager->syncPermissions([
            'reports.view', 'reports.export',
        ]);

        $support = Role::query()->firstOrCreate(
            ['name' => 'support', 'guard_name' => 'sanctum']
        );
        $support->syncPermissions([
            'users.view',
        ]);

        $viewer = Role::query()->firstOrCreate(
            ['name' => 'viewer', 'guard_name' => 'sanctum']
        );
        $viewer->syncPermissions([
            'settings.view',
            'reports.view',
        ]);

        $this->command->info('Created 5 roles with permissions.');
    }
}
