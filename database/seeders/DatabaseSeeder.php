<?php

namespace Database\Seeders;

use App\Models\Central\User;
use Database\Seeders\Central\NotificationSeeder;
use Database\Seeders\Central\PlanSeeder;
use Database\Seeders\Central\SettingSeeder;
use Database\Seeders\Central\TenantSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            SettingSeeder::class,
            TenantSeeder::class,
            NotificationSeeder::class,
        ]);
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
    }
}
