<?php

namespace App\Repositories\Central;

use App\Models\Central\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubscriptionRepository
{
    /**
     * Get all subscriptions with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Subscription::class)
            ->allowedFilters(
                AllowedFilter::exact('tenant_id'),
                AllowedFilter::exact('plan_id'),
                AllowedFilter::exact('status'),
            )
            ->allowedSorts('starts_at', 'ends_at', 'status', 'created_at')
            ->with(['tenant', 'plan'])
            ->paginate($perPage);
    }

    /**
     * Create a new subscription.
     *
     * @param array $data
     * @return Subscription
     */
    public function create(array $data): Subscription
    {
        return Subscription::query()->create($data);
    }

    /**
     * Update an existing subscription.
     *
     * @param Subscription $subscription
     * @param array $data
     * @return Subscription
     */
    public function update(Subscription $subscription, array $data): Subscription
    {
        $subscription->update($data);
        return $subscription;
    }

    /**
     * Get active subscriptions for a tenant.
     *
     * @param string $tenantId
     * @return Collection
     */
    public function getActiveSubscriptionsByTenant(string $tenantId): Collection
    {
         return Subscription::query()->where('tenant_id', $tenantId)
            ->where('status', \App\Enums\SubscriptionStatus::ACTIVE->value)
            ->get();
    }
}
