<?php

namespace App\Repositories\Central;

use App\Models\Central\Plan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PlanRepository
{
    /**
     * Get all plans with optional filters and pagination.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function findAll(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Plan::class)
            ->allowedFilters(
                AllowedFilter::exact('is_active'),
                AllowedFilter::partial('name'),
            )
            ->allowedSorts('name', 'price', 'created_at')
            ->paginate(
                $request->integer('per_page', 15),
                page: $request->integer('page', 1)
            );
    }

    /**
     * Get active plans for dropdown.
     *
     * @return Collection
     */
    public function getActivePlansForDropdown(): Collection
    {
        return Plan::query()->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($plan) {
                return [
                    'value' => $plan->id,
                    'label' => "{$plan->name} (\${$plan->price})",
                ];
            });
    }

    /**
     * Create a new plan.
     *
     * @param array $data
     * @return Plan
     */
    public function create(array $data): Plan
    {
        return Plan::query()->create($data);
    }

    /**
     * Update an existing plan.
     *
     * @param Plan $plan
     * @param array $data
     * @return Plan
     */
    public function update(Plan $plan, array $data): Plan
    {
        $plan->update($data);
        return $plan;
    }

    /**
     * Delete a plan.
     *
     * @param Plan $plan
     * @return bool
     */
    public function delete(Plan $plan): bool
    {
        return $plan->delete();
    }
}
