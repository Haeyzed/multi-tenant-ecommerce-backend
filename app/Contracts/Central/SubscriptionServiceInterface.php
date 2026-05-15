<?php

namespace App\Contracts\Central;

use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubscriptionServiceInterface
{
    /**
     * Get all subscriptions.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllSubscriptions(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Subscribe a tenant to a plan.
     *
     * @param Tenant $tenant
     * @param array $data
     * @return Subscription
     */
    public function subscribeTenantToPlan(Tenant $tenant, array $data): Subscription;

    /**
     * Cancel an active subscription.
     *
     * @param Subscription $subscription
     * @return Subscription
     */
    public function cancelSubscription(Subscription $subscription): Subscription;

    /**
     * Check if a tenant has an active subscription.
     *
     * @param Tenant $tenant
     * @return bool
     */
    public function checkSubscriptionStatus(Tenant $tenant): bool;
}
