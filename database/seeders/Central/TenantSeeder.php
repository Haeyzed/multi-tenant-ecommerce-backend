<?php

namespace Database\Seeders\Central;

use App\Models\Central\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant1 = Tenant::query()->create([
            'id' => 'tenant1',
            'name' => 'Tenant 1',
            'email' => 'tenant1@example.com',
            'plan_id' => 1,
            'status' => 'active',
        ]);
        $tenant1->domains()->create(['domain' => 'tenant1.localhost']);

        $tenant2 = Tenant::query()->create([
            'id' => 'tenant2',
            'name' => 'Tenant 2',
            'email' => 'tenant2@example.com',
            'plan_id' => 2,
            'status' => 'active',
        ]);
        $tenant2->domains()->create(['domain' => 'tenant2.localhost']);
    }
}
