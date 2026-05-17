<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Central\Domain;
use Illuminate\Console\Command;

class NormalizeTenantDomainsCommand extends Command
{
    protected $signature = 'tenants:normalize-domains
                            {--dry-run : Show changes without saving}';

    protected $description = 'Normalize tenant domain hosts to match TENANT_DOMAIN_BASE (e.g. slug.test instead of slug.localhost)';

    public function handle(): int
    {
        $base = (string) config('tenancy.tenant_domain_base'); // TENANT_DOMAIN_SUFFIX
        $dryRun = (bool) $this->option('dry-run');

        if ($base === '') {
            $this->error('tenancy.tenant_domain_base is empty. Set TENANT_DOMAIN_BASE in .env');

            return self::FAILURE;
        }

        $this->info("Target domain base: {$base}");
        if ($dryRun) {
            $this->warn('Dry run — no records will be updated.');
        }

        $updated = 0;

        Domain::query()->orderBy('id')->each(function (Domain $domain) use ($base, $dryRun, &$updated) {
            $normalized = $this->normalizeHost($domain->domain, $base);

            if ($normalized === $domain->domain) {
                return;
            }

            if (Domain::query()->where('domain', $normalized)->where('id', '!=', $domain->id)->exists()) {
                $this->error("Cannot update domain #{$domain->id}: {$normalized} already exists.");

                return;
            }

            $this->line("  [{$domain->tenant_id}] {$domain->domain} → {$normalized}");

            if (! $dryRun) {
                Domain::withoutEvents(function () use ($domain, $normalized): void {
                    $domain->update(['domain' => $normalized]);
                });
            }

            $updated++;
        });

        $this->newLine();
        $this->info($dryRun
            ? "Would update {$updated} domain(s). Run without --dry-run to apply."
            : "Updated {$updated} domain(s).");

        return self::SUCCESS;
    }

    private function normalizeHost(string $domain, string $base): string
    {
        $domain = strtolower(trim($domain));

        $doubleBase = ".{$base}.{$base}";
        $domain = str_replace($doubleBase, ".{$base}", $domain);

        if ($domain === '' || $domain === $base) {
            return $domain;
        }

        if (str_ends_with($domain, ".{$base}")) {
            return $domain;
        }

        if (! str_contains($domain, '.')) {
            return "{$domain}.{$base}";
        }

        if (str_ends_with($domain, '.localhost')) {
            $slug = substr($domain, 0, -strlen('.localhost'));

            return "{$slug}.{$base}";
        }

        return $domain;
    }
}
