<?php

namespace App\Models\Central;

use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'status',
        'plan',
        'trial_ends_at',
        'data',
    ];

    /**
     * Get the attributes that should be cast.
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
     * Get the custom columns for the model.
     *
     * @return array<string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'status',
            'plan',
            'trial_ends_at',
        ];
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === TenantStatus::ACTIVE;
    }

    /**
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->status === TenantStatus::SUSPENDED;
    }

    /**
     * @return void
     */
    public function suspend(): void
    {
        $this->update(['status' => TenantStatus::SUSPENDED]);
    }

    /**
     * @return void
     */
    public function activate(): void
    {
        $this->update(['status' => TenantStatus::ACTIVE]);
    }
}
