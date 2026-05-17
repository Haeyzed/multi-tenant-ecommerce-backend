<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Services\Central\LocalDevelopment\TenantHostRegistrar;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Runs Herd link/unlink after the HTTP response is sent (via afterResponse()).
 * Not queued — avoids needing a worker for local tenant creation.
 */
class RegisterTenantHostJob
{
    use Queueable;

    public function __construct(
        public readonly string $host,
        public readonly bool $unregister = false,
    ) {}

    public function handle(TenantHostRegistrar $registrar): void
    {
        if ($this->unregister) {
            $registrar->registerSync($this->host, force: true, unlink: true);

            return;
        }

        $registrar->registerSync($this->host, force: true);
    }
}
