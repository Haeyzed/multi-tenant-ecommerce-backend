<?php

declare(strict_types=1);

namespace App\Models\Central;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * Class Domain
 *
 * Custom domain model for tenant routing.
 *
 * @property int $id
 * @property string $domain
 * @property string $tenant_id
 * @property bool $is_primary
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Tenant $tenant
 */
class Domain extends BaseDomain
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'domain',
        'tenant_id',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];
}
