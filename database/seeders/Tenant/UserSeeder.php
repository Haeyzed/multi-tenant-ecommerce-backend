<?php

declare(strict_types=1);

namespace Database\Seeders\Tenant;

use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds default store users with roles for local/testing (mirrors Central\UserSeeder).
 */
class UserSeeder extends Seeder
{
    private const string DEFAULT_PASSWORD = 'password';

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createUsers();

        $this->command->info('Tenant users seeded successfully with roles.');
    }

    private function createUsers(): void
    {
        $superAdmin = User::query()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@store.test',
            'phone' => '+234 800 100 0001',
            'password' => self::DEFAULT_PASSWORD,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        $this->command->info('Super Admin created: superadmin@store.test / '.self::DEFAULT_PASSWORD);

        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@store.test',
            'phone' => '+234 800 100 0002',
            'password' => self::DEFAULT_PASSWORD,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $this->command->info('Admin created: admin@store.test / '.self::DEFAULT_PASSWORD);

        $billingManager = User::query()->create([
            'name' => 'Billing Manager',
            'email' => 'billing@store.test',
            'phone' => '+234 800 100 0003',
            'password' => self::DEFAULT_PASSWORD,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $billingManager->assignRole('billing_manager');

        $this->command->info('Billing Manager created: billing@store.test / '.self::DEFAULT_PASSWORD);

        $supportAgent = User::query()->create([
            'name' => 'Support Agent',
            'email' => 'support@store.test',
            'phone' => '+234 800 100 0004',
            'password' => self::DEFAULT_PASSWORD,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $supportAgent->assignRole('support');

        $this->command->info('Support created: support@store.test / '.self::DEFAULT_PASSWORD);

        $inactiveUser = User::query()->create([
            'name' => 'Inactive User',
            'email' => 'inactive@store.test',
            'phone' => '+234 800 100 0005',
            'password' => self::DEFAULT_PASSWORD,
            'is_active' => false,
            'email_verified_at' => now(),
        ]);
        $inactiveUser->assignRole('viewer');

        $this->command->info('Inactive User created: inactive@store.test / '.self::DEFAULT_PASSWORD);

        $unverifiedUser = User::query()->create([
            'name' => 'Unverified User',
            'email' => 'unverified@store.test',
            'phone' => '+234 800 100 0006',
            'password' => self::DEFAULT_PASSWORD,
            'is_active' => true,
            'email_verified_at' => null,
        ]);
        $unverifiedUser->assignRole('viewer');

        $this->command->info('Unverified User created: unverified@store.test / '.self::DEFAULT_PASSWORD);

        $this->command->newLine();
        $this->command->warn('Default password for all seeded users: '.self::DEFAULT_PASSWORD);
    }
}
