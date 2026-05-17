<?php

namespace Database\Seeders\Central;

use App\Models\Central\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'price' => 10.00,
                'features' => ['products', 'orders', 'customers'],
                'limits' => [
                    'products' => 100,
                    'orders_per_month' => 500,
                    'staff' => 5,
                    'storage_gb' => 10,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'price' => 29.00,
                'features' => ['products', 'orders', 'customers', 'inventory', 'analytics'],
                'limits' => [
                    'products' => 1000,
                    'orders_per_month' => 5000,
                    'staff' => 20,
                    'storage_gb' => 50,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'price' => 99.00,
                'features' => [
                    'products', 'orders', 'customers', 'inventory', 'analytics',
                    'api_access', 'white_label', 'priority_support',
                ],
                'limits' => [
                    'products' => -1,
                    'orders_per_month' => -1,
                    'staff' => -1,
                    'storage_gb' => -1,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->create($plan);
        }
    }
}
