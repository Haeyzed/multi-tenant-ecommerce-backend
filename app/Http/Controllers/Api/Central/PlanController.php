<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\DTOs\Central\PlanData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreatePlanRequest;
use App\Http\Requests\Central\UpdatePlanRequest;
use App\Models\Central\Plan;
use App\Services\Central\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PlanController
 *
 * Handles subscription plan management.
 *
 * @package App\Http\Controllers\Api\Central
 */
class PlanController extends Controller
{
    /**
     * @param PlanService $planService
     */
    public function __construct(
        private readonly PlanService $planService
    ) {}

    /**
     * Get all plans.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $plans = $this->planService->getAllPlans($request);

        return response()->json([
            'success' => true,
            'message' => 'Plans retrieved successfully',
            'data' => PlanData::collect($plans->items()),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ],
        ]);
    }

    /**
     * Get active plans for dropdown.
     *
     * @return JsonResponse
     */
    public function dropdown(): JsonResponse
    {
        $plans = $this->planService->getActivePlansForDropdown();

        return response()->json([
            'success' => true,
            'message' => 'Active plans retrieved successfully',
            'data' => $plans,
        ]);
    }

    /**
     * Create a new plan.
     *
     * @param CreatePlanRequest $request
     * @return JsonResponse
     */
    public function store(CreatePlanRequest $request): JsonResponse
    {
        $dto = PlanData::from($request->validated());
        $plan = $this->planService->createPlan($dto);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => PlanData::from($plan),
        ], 201);
    }

    /**
     * Display the specified plan.
     *
     * @param Plan $plan
     * @return JsonResponse
     */
    public function show(Plan $plan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Plan retrieved successfully',
            'data' => PlanData::from($plan),
        ]);
    }

    /**
     * Update the specified plan.
     *
     * @param UpdatePlanRequest $request
     * @param Plan $plan
     * @return JsonResponse
     */
    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        $dto = PlanData::from([...$plan->toArray(), ...$request->validated()]);
        $updatedPlan = $this->planService->updatePlan($plan, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => PlanData::from($updatedPlan),
        ]);
    }

    /**
     * Remove the specified plan.
     *
     * @param Plan $plan
     * @return JsonResponse
     */
    public function destroy(Plan $plan): JsonResponse
    {
        $this->planService->deletePlan($plan);

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully'
        ]);
    }
}
