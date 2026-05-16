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
                'features' => json_encode([
                    'storage' => '10GB',
                    'users' => 5,
                ]),
                'limits' => json_encode([
                    'products' => 100,
                    'orders_per_month' => 500,
                ]),
            ],
            [
                'name' => 'Pro',
                'price' => 29.00,
                'features' => json_encode([
                    'storage' => '50GB',
                    'users' => 20,
                ]),
                'limits' => json_encode([
                    'products' => 1000,
                    'orders_per_month' => 5000,
                ]),
            ],
            [
                'name' => 'Premium',
                'price' => 99.00,
                'features' => json_encode([
                    'storage' => 'Unlimited',
                    'users' => 'Unlimited',
                ]),
                'limits' => json_encode([
                    'products' => -1, // -1 represents unlimited
                    'orders_per_month' => -1,
                ]),
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->create($plan);
        }
    }
}
