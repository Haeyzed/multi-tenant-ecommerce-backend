<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Central\Domain;
use App\Services\Central\LocalDevelopment\TenantHostRegistrar;
use Illuminate\Console\Command;

class RegisterTenantHostsCommand extends Command
{
    protected $signature = 'tenants:register-hosts
                            {--unregister : Remove Herd links for all tenant domains}';

    protected $description = 'Register (or unregister) all tenant domains with Laravel Herd for local development';

    public function handle(TenantHostRegistrar $registrar): int
    {
        if (! app()->environment('local')) {
            $this->error('This command is only available when APP_ENV=local.');

            return self::FAILURE;
        }

        $domains = Domain::query()->orderBy('domain')->pluck('domain');

        if ($domains->isEmpty()) {
            $this->warn('No tenant domains found.');

            return self::SUCCESS;
        }

        $unregister = (bool) $this->option('unregister');
        $action = $unregister ? 'unregister' : 'register';
        $count = 0;

        foreach ($domains as $host) {
            $ok = $unregister
                ? $registrar->registerSync($host, force: true, unlink: true)
                : $registrar->registerSync($host, force: true);

            if ($ok) {
                $count++;
                $this->line("  [ok] {$host}");
            } else {
                $this->line("  [..] {$host} (skipped or already registered — check storage/logs/laravel.log)");
            }
        }

        $this->newLine();
        $this->info("Processed {$domains->count()} domain(s); {$action} reported success for {$count}.");

        if (! $unregister) {
            $this->comment('Ensure TENANT_AUTO_REGISTER_HOSTS=true in .env for automatic registration on new tenants.');
        }

        return self::SUCCESS;
    }
}
