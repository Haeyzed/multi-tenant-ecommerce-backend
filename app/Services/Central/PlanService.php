<?php

namespace App\Services\Central;

use App\DTOs\Central\PlanData;
use App\Models\Central\Plan;
use App\Repositories\Central\PlanRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

readonly class PlanService
{
    /**
     * PlanService constructor.
     *
     * @param PlanRepository $repository
     */
    public function __construct(
        private PlanRepository $repository
    ) {}

    /**
     * Get all plans with pagination.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAllPlans(Request $request): LengthAwarePaginator
    {
        return $this->repository->findAll($request);
    }

    /**
     * Get active plans for dropdown.
     *
     * @return Collection|\Illuminate\Support\Collection
     */
    public function getActivePlansForDropdown(): Collection|\Illuminate\Support\Collection
    {
        return $this->repository->getActivePlansForDropdown();
    }

    /**
     * Create a new plan.
     *
     * @param PlanData $planData
     * @return Plan
     */
    public function createPlan(PlanData $planData): Plan
    {
        return $this->repository->create($planData->toArray());
    }

    /**
     * Update an existing plan.
     *
     * @param Plan $plan
     * @param PlanData $planData
     * @return Plan
     */
    public function updatePlan(Plan $plan, PlanData $planData): Plan
    {
        return $this->repository->update($plan, $planData->toArray());
    }

    /**
     * Delete a plan.
     *
     * @param Plan $plan
     * @return bool
     */
    public function deletePlan(Plan $plan): bool
    {
        return $this->repository->delete($plan);
    }
}
