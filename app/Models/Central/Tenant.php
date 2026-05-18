<?php

declare(strict_types=1);

namespace App\Models\Central;

use App\Enums\Central\TenantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Class Tenant
 *
 * Central tenant model extending Stancl's base tenant.
 * Manages multi-tenant database provisioning, domains, and billing.
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property TenantStatus $status
 * @property int|null $plan_id
 * @property \Carbon\Carbon|null $trial_ends_at
 * @property array|null $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stancl\Tenancy\Database\Models\Domain[] $domains
 * @property-read Plan|null $plan
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'status',
        'plan_id',
        'trial_ends_at',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TenantStatus::class,
            'trial_ends_at' => 'datetime',
            'data' => 'array',
        ];
    }

    /**
     * Get the custom columns for the tenant model.
     *
     * These columns are stored in the tenants table alongside
     * the default id and data columns provided by Stancl.
     *
     * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'status',
            'plan_id',
            'trial_ends_at',
        ];
    }

    /**
     * Check if the tenant is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === TenantStatus::ACTIVE;
    }

    /**
     * Check if the tenant is suspended.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->status === TenantStatus::SUSPENDED;
    }

    /**
     * Suspend the tenant.
     *
     * @return void
     */
    public function suspend(): void
    {
        $this->update(['status' => TenantStatus::SUSPENDED]);
    }

    /**
     * Activate the tenant.
     *
     * @return void
     */
    public function activate(): void
    {
        $this->update(['status' => TenantStatus::ACTIVE]);
    }

    /**
     * Get the subscription plan associated with the tenant.
     *
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the subscriptions for the tenant.
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the invoices for the tenant.
     *
     * @return HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
