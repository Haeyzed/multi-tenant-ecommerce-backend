<?php

namespace App\Contracts\Central;

use App\DTOs\Central\PlanDTO;
use App\Models\Central\Plan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PlanServiceInterface
{
    /**
     * Get all plans with pagination.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAllPlans(Request $request): LengthAwarePaginator;

    /**
     * Get active plans for dropdown.
     *
     * @return Collection
     */
    public function getActivePlansForDropdown(): Collection;

    /**
     * Create a new plan.
     *
     * @param PlanDTO $dto
     * @return Plan
     */
    public function createPlan(PlanDTO $dto): Plan;

    /**
     * Update an existing plan.
     *
     * @param Plan $plan
     * @param PlanDTO $dto
     * @return Plan
     */
    public function updatePlan(Plan $plan, PlanDTO $dto): Plan;

    /**
     * Delete a plan.
     *
     * @param Plan $plan
     * @return bool
     */
    public function deletePlan(Plan $plan): bool;
}
