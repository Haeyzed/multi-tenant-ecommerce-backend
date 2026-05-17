<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Central\Tenant;
use Database\Seeders\Tenant\NotificationSeeder;
use Illuminate\Console\Command;

class SeedTenantNotificationsCommand extends Command
{
    protected $signature = 'tenants:seed-notifications
                            {--tenant= : Seed a single tenant by ID}';

    protected $description = 'Seed notification channels and templates for existing tenant databases';

    public function handle(): int
    {
        $tenantId = $this->option('tenant');

        $tenants = $tenantId
            ? Tenant::query()->whereKey($tenantId)->get()
            : Tenant::query()->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');

            return self::FAILURE;
        }

        foreach ($tenants as $tenant) {
            $this->info("Seeding notifications for tenant: {$tenant->id} ({$tenant->name})");

            $tenant->run(function () {
                $seeder = new NotificationSeeder;
                $seeder->setCommand($this);
                $seeder->run();
            });
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
