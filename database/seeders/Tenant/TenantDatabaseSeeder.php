<?php

namespace Database\Seeders\Tenant;

use Database\Seeders\Tenant\RolePermissionSeeder;
use Database\Seeders\Tenant\SettingSeeder;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant database after provisioning.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            NotificationSeeder::class,
            UserSeeder::class,
        ]);
    }
}
