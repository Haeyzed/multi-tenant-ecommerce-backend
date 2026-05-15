<?php

namespace App\Services\Central;

use App\Contracts\Central\SubscriptionServiceInterface;
use App\Contracts\Central\InvoiceServiceInterface;
use App\Contracts\Central\PaymentServiceInterface;
use App\Enums\SubscriptionStatus;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Models\Central\Plan;
use App\Repositories\Central\SubscriptionRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class SubscriptionService implements SubscriptionServiceInterface
{
    /**
     * SubscriptionService constructor.
     *
     * @param SubscriptionRepository $repository
     * @param InvoiceServiceInterface $invoiceService
     * @param PaymentServiceInterface $paymentService
     */
    public function __construct(
        private SubscriptionRepository $repository,
        private InvoiceServiceInterface $invoiceService,
        private PaymentServiceInterface $paymentService
    ) {}

    /**
     * Get all subscriptions.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllSubscriptions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Subscribe a tenant to a plan and process payment.
     *
     * @param Tenant $tenant
     * @param array $data
     * @return Subscription
     * @throws Exception
     */
    public function subscribeTenantToPlan(Tenant $tenant, array $data): Subscription
    {
        $plan = Plan::findOrFail($data['plan_id']);

        return DB::transaction(function () use ($tenant, $plan, $data) {
            // Cancel existing active subscriptions
            $activeSubscriptions = $this->repository->getActiveSubscriptionsByTenant($tenant->id);

            foreach ($activeSubscriptions as $subscription) {
                $this->repository->update($subscription, [
                    'status' => SubscriptionStatus::CANCELLED->value,
                    'ends_at' => Carbon::now(),
                ]);
            }

            // Create new subscription
            $subscription = $this->repository->create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::ACTIVE->value,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonth(), // Monthly billing
            ]);

            // Generate Invoice
            $invoice = $this->invoiceService->generateInvoiceForSubscription($tenant, $plan);

            // Process Payment
            $this->paymentService->processPayment(
                $invoice,
                $data['payment_gateway'],
                $data['transaction_id'] ?? null
            );

            return $subscription;
        });
    }

    /**
     * Cancel an active subscription.
     *
     * @param Subscription $subscription
     * @return Subscription
     */
    public function cancelSubscription(Subscription $subscription): Subscription
    {
        return $this->repository->update($subscription, [
            'status' => SubscriptionStatus::CANCELLED->value,
            'ends_at' => Carbon::now(),
        ]);
    }

    /**
     * Check if a tenant has an active subscription.
     *
     * @param Tenant $tenant
     * @return bool
     */
    public function checkSubscriptionStatus(Tenant $tenant): bool
    {
        $activeSubscriptions = $this->repository->getActiveSubscriptionsByTenant($tenant->id);

        return $activeSubscriptions->where('ends_at', '>', Carbon::now())->isNotEmpty();
    }
}
