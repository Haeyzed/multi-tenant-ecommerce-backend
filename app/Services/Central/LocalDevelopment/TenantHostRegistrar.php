<?php

declare(strict_types=1);

namespace App\Services\Central\LocalDevelopment;

use App\Jobs\Central\RegisterTenantHostJob;
use App\Support\Tenancy\TenantDomain;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Registers tenant hostnames with Laravel Herd on local development (Windows/macOS).
 *
 * Uses `herd link {name}` from the application root so entries appear under
 * "Herd generated Hosts" without manually editing the system hosts file.
 */
class TenantHostRegistrar
{
    /**
     * Queue Herd registration so tenant API responses are not blocked (herd link can take 30s+ on Windows).
     */
    public function register(string $host, bool $force = false): bool
    {
        if (! $force && ! $this->isEnabled()) {
            return false;
        }

        if (! app()->environment('local')) {
            return false;
        }

        if ($force || ! (bool) config('tenancy.local_dev.register_hosts_after_response', true)) {
            return $this->registerSync($host, $force);
        }

        RegisterTenantHostJob::dispatch($host)->afterResponse();

        Log::info('Tenant host registration queued (runs after HTTP response).', ['host' => $host]);

        return true;
    }

    public function unregister(string $host, bool $force = false): bool
    {
        if (! $force) {
            if (! $this->isEnabled() || ! (bool) config('tenancy.local_dev.auto_unregister_hosts', true)) {
                return false;
            }
        }

        if (! app()->environment('local')) {
            return false;
        }

        if ($force || ! (bool) config('tenancy.local_dev.register_hosts_after_response', true)) {
            return $this->registerSync($host, $force, unlink: true);
        }

        RegisterTenantHostJob::dispatch($host, unregister: true)->afterResponse();

        return true;
    }

    public function registerSync(string $host, bool $force = false, bool $unlink = false): bool
    {
        if (! app()->environment('local')) {
            return false;
        }

        if (! $force && ! $this->isEnabled() && ! $unlink) {
            return false;
        }

        if (! $this->herdIsAvailable()) {
            Log::warning('Tenant host was not registered: Herd CLI not found.', [
                'host' => $host,
                'hint' => 'Install Laravel Herd and ensure `herd` is on your PATH, or set TENANT_AUTO_REGISTER_HOSTS=false.',
            ]);

            return false;
        }

        return $this->runHerdCommand(
            $unlink ? 'unlink' : 'link',
            TenantDomain::herdLinkName($host),
            $host
        );
    }

    private function isEnabled(): bool
    {
        return app()->environment('local')
            && (bool) config('tenancy.local_dev.auto_register_hosts', false);
    }

    private function herdIsAvailable(): bool
    {
        $binary = $this->herdBinary();

        if (PHP_OS_FAMILY === 'Windows') {
            $process = Process::fromShellCommandline("where {$binary}");
        } else {
            $process = Process::fromShellCommandline("command -v {$binary}");
        }

        $process->setTimeout(5);
        $process->run();

        return $process->isSuccessful();
    }

    private function runHerdCommand(string $command, string $linkName, string $host): bool
    {
        $binary = $this->herdBinary();
        $timeout = (int) config('tenancy.local_dev.herd_timeout_seconds', 120);

        $process = new Process(
            [$binary, $command, $linkName],
            base_path(),
            null,
            null,
            $timeout
        );

        $process->run();

        if ($process->isSuccessful()) {
            Log::info("Tenant host {$command}ed via Herd.", [
                'host' => $host,
                'herd_name' => $linkName,
            ]);

            return true;
        }

        $output = trim($process->getErrorOutput()."\n".$process->getOutput());

        if ($this->isBenignHerdMessage($output)) {
            Log::info('Tenant host already registered in Herd.', ['host' => $host]);

            return true;
        }

        Log::warning("Herd {$command} failed for tenant host.", [
            'host' => $host,
            'herd_name' => $linkName,
            'output' => $output,
        ]);

        return false;
    }

    private function isBenignHerdMessage(string $output): bool
    {
        $normalized = strtolower($output);

        return str_contains($normalized, 'already')
            || str_contains($normalized, 'exists')
            || str_contains($normalized, 'has been linked');
    }

    private function herdBinary(): string
    {
        $binary = (string) config('tenancy.local_dev.herd_binary', 'herd');

        if (PHP_OS_FAMILY === 'Windows' && ! str_ends_with(strtolower($binary), '.bat')) {
            return $binary.'.bat';
        }

        return $binary;
    }
}
