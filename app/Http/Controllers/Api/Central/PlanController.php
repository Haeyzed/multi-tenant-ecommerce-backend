<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\DTOs\Central\PlanData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreatePlanRequest;
use App\Http\Requests\Central\UpdatePlanRequest;
use App\Models\Central\Plan;
use App\Services\Central\PlanService;
use App\Traits\ApiResponse;
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
    use ApiResponse;

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

        return $this->successResponse(
            'Plans retrieved successfully',
            PlanData::collect($plans->items()),
            200,
            [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ]
        );
    }

    /**
     * Get active plans for dropdown.
     *
     * @return JsonResponse
     */
    public function dropdown(): JsonResponse
    {
        $plans = $this->planService->getActivePlansForDropdown();

        return $this->successResponse('Active plans retrieved successfully', $plans);
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

        return $this->successResponse(
            'Plan created successfully',
            PlanData::from($plan),
            201
        );
    }

    /**
     * Display the specified plan.
     *
     * @param Plan $plan
     * @return JsonResponse
     */
    public function show(Plan $plan): JsonResponse
    {
        return $this->successResponse(
            'Plan retrieved successfully',
            PlanData::from($plan)
        );
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

        return $this->successResponse(
            'Plan updated successfully',
            PlanData::from($updatedPlan)
        );
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

        return $this->successResponse('Plan deleted successfully');
    }
}
