<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Notifications\CentralNotificationTemplateCatalog;
use Illuminate\Console\Command;

class SyncCentralNotificationTemplatesCommand extends Command
{
    protected $signature = 'central:sync-notification-templates';

    protected $description = 'Sync missing central notification email templates into the database';

    public function handle(): int
    {
        $created = CentralNotificationTemplateCatalog::syncMissing();

        $this->info("Synced central notification templates ({$created} new).");

        return self::SUCCESS;
    }
}
