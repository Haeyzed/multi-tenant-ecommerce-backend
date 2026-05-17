<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Central\Tenant;
use App\Support\Notifications\TenantNotificationTemplateCatalog;
use Illuminate\Console\Command;

class SyncTenantNotificationTemplatesCommand extends Command
{
    protected $signature = 'tenants:sync-notification-templates
                            {--tenant= : Sync a single tenant by ID}';

    protected $description = 'Sync missing tenant notification email templates into each tenant database';

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
            $this->info("Syncing notification templates for tenant: {$tenant->id} ({$tenant->name})");

            $tenant->run(function () {
                $created = TenantNotificationTemplateCatalog::syncMissing();
                $this->line("  → {$created} new template(s).");
            });
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
