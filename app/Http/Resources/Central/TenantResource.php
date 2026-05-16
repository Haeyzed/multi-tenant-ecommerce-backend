<?php

declare(strict_types=1);

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TenantResource
 *
 * Transforms tenant model into API response format.
 *
 * @package App\Http\Resources\Central
 */
class TenantResource extends JsonResource
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
             * The tenant unique identifier (UUID).
             * @var string $id
             * @example "550e8400-e29b-41d4-a716-446655440000"
             */
            'id' => $this->id,

            /**
             * The business/organization name.
             * @var string $name
             * @example "Green Mart Nigeria"
             */
            'name' => $this->name,

            /**
             * The tenant business email.
             * @var string $email
             * @example "info@greenmart.ng"
             */
            'email' => $this->email,

            /**
             * The tenant business phone.
             * @var string|null $phone
             * @example "+234 800 123 4567"
             */
            'phone' => $this->phone,

            /**
             * The tenant account status value.
             * @var string $status
             * @example "active"
             */
            'status' => $this->status->value,

            /**
             * The human-readable status label.
             * @var string $status_label
             * @example "Active"
             */
            'status_label' => $this->status->label(),

            /**
             * The assigned subscription plan.
             * @var array<string, mixed>|null $plan
             * @example {"id": 2, "name": "Premium Plan", "price": 49.99}
             */
            'plan' => $this->whenLoaded('plan', function () {
                return [
                    /**
                     * The plan unique identifier.
                     * @var int $id
                     * @example 2
                     */
                    'id' => $this->plan->id,

                    /**
                     * The plan display name.
                     * @var string $name
                     * @example "Premium Plan"
                     */
                    'name' => $this->plan->name,

                    /**
                     * The monthly subscription price.
                     * @var float $price
                     * @example 49.99
                     */
                    'price' => (float) $this->plan->price,

                    /**
                     * List of included features.
                     * @var array<string>|null $features
                     * @example ["unlimited_products", "advanced_analytics", "priority_support"]
                     */
                    'features' => $this->plan->features,

                    /**
                     * Usage limits for the plan.
                     * @var array<string, int>|null $limits
                     * @example {"products": 100, "staff": 5, "storage_gb": 10}
                     */
                    'limits' => $this->plan->limits,

                    /**
                     * Whether the plan is currently available.
                     * @var bool $is_active
                     * @example true
                     */
                    'is_active' => (bool) $this->plan->is_active,
                ];
            }),

            /**
             * The trial period end date.
             * @var string|null $trial_ends_at
             * @example "2026-06-16 23:59:59"
             */
            'trial_ends_at' => $this->trial_ends_at?->toDateTimeString(),

            /**
             * The tenant associated domains.
             * @var \Illuminate\Http\Resources\Json\AnonymousResourceCollection|null $domains
             */
            'domains' => DomainResource::collection($this->whenLoaded('domains')),

            /**
             * The tenant creation timestamp.
             * @var string $created_at
             * @example "2026-05-16 10:00:00"
             */
            'created_at' => $this->created_at->toDateTimeString(),

            /**
             * The tenant last update timestamp.
             * @var string $updated_at
             * @example "2026-05-16 14:30:00"
             */
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
