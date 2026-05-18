<?php

namespace App\Services\Central;

use App\Contracts\Central\PlanServiceInterface;
use App\DTOs\Central\PlanDTO;
use App\Models\Central\Plan;
use App\Repositories\Central\PlanRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

readonly class PlanService implements PlanServiceInterface
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
     * @return Collection
     */
    public function getActivePlansForDropdown(): Collection
    {
        return $this->repository->getActivePlansForDropdown();
    }

    /**
     * Create a new plan.
     *
     * @param PlanDTO $dto
     * @return Plan
     */
    public function createPlan(PlanDTO $dto): Plan
    {
        return $this->repository->create($dto->toArray());
    }

    /**
     * Update an existing plan.
     *
     * @param Plan $plan
     * @param PlanDTO $dto
     * @return Plan
     */
    public function updatePlan(Plan $plan, PlanDTO $dto): Plan
    {
        return $this->repository->update($plan, $dto->toArray());
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
