<?php

declare(strict_types=1);

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PlanResource
 *
 * Transforms subscription plan model into API response format.
 *
 * @package App\Http\Resources\Central
 */
class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The plan unique identifier.
             * @var int $id
             * @example 2
             */
            'id' => $this->id,

            /**
             * The plan display name.
             * @var string $name
             * @example "Premium Plan"
             */
            'name' => $this->name,

            /**
             * The monthly subscription price.
             * @var float $price
             * @example 49.99
             */
            'price' => (float) $this->price,

            /**
             * List of included features.
             * @var array<string>|null $features
             * @example ["unlimited_products", "advanced_analytics", "priority_support"]
             */
            'features' => $this->features,

            /**
             * Usage limits for the plan.
             * @var array<string, int>|null $limits
             * @example {"products": 100, "staff": 5, "storage_gb": 10}
             */
            'limits' => $this->limits,

            /**
             * Whether the plan is currently available.
             * @var bool $is_active
             * @example true
             */
            'is_active' => (bool) $this->is_active,

            /**
             * The plan creation timestamp.
             * @var string $created_at
             * @example "2026-05-10 09:00:00"
             */
            'created_at' => $this->created_at,

            /**
             * The plan last update timestamp.
             * @var string $updated_at
             * @example "2026-05-15 16:45:00"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
