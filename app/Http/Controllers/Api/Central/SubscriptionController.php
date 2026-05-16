<?php

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\SubscriptionServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreateSubscriptionRequest;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * @param SubscriptionServiceInterface $subscriptionService
     */
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {}

    /**
     * Get all subscriptions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $subscriptions = $this->subscriptionService->getAllSubscriptions(
            $request->all(),
            $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscriptions retrieved successfully',
            'data' => $subscriptions->items(),
            'meta' => [
                'current_page' => $subscriptions->currentPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
            ],
        ]);
    }

    /**
     * Subscribe a tenant to a plan.
     *
     * @param CreateSubscriptionRequest $request
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function store(CreateSubscriptionRequest $request, Tenant $tenant): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->subscribeTenantToPlan($tenant, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tenant subscribed successfully',
                'data' => $subscription
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel a subscription.
     *
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function cancel(Subscription $subscription): JsonResponse
    {
        $cancelledSubscription = $this->subscriptionService->cancelSubscription($subscription);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
            'data' => $cancelledSubscription
        ]);
    }

    /**
     * Check active subscription status for a tenant.
     *
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function checkStatus(Tenant $tenant): JsonResponse
    {
        $isActive = $this->subscriptionService->checkSubscriptionStatus($tenant);

        return response()->json([
            'success' => true,
            'message' => 'Subscription status checked',
            'data' => [
                'has_active_subscription' => $isActive
            ]
        ]);
    }
}
