<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

/**
 * Builds tenant hostnames for Stancl domain identification.
 *
 * Pattern: {tenant_slug}.{TENANT_DOMAIN_SUFFIX}
 * Example: globalhub.ecommerce-application-backend.test
 */
final class TenantDomain
{
    public static function suffix(): string
    {
        return (string) config('tenancy.tenant_domain_base');
    }

    public static function tld(): string
    {
        return (string) config('tenancy.local_dev.domain_tld', 'test');
    }

    /**
     * Turn API subdomain slug into a full host stored in domains.domain.
     */
    public static function qualify(string $domain): string
    {
        $domain = strtolower(trim($domain));

        if ($domain === '' || str_contains($domain, '.')) {
            return $domain;
        }

        return "{$domain}.".self::suffix();
    }

    /**
     * Herd link name (domain without .test TLD).
     */
    public static function herdLinkName(string $host): string
    {
        $host = strtolower(trim($host));
        $tldSuffix = '.'.self::tld();

        if (str_ends_with($host, $tldSuffix)) {
            return substr($host, 0, -strlen($tldSuffix));
        }

        return $host;
    }
}
