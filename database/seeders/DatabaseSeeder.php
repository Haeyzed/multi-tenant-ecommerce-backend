<?php

namespace Database\Seeders;

use App\Models\Central\User;
use Database\Seeders\Central\PlanTableSeeder;
use Database\Seeders\Central\SettingTableSeeder;
use Database\Seeders\Central\TenantTableSeeder;
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
            SettingTableSeeder::class,
            PlanTableSeeder::class,
            TenantTableSeeder::class,
        ]);
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
    }
}
