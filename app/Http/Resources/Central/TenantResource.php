<?php

declare(strict_types=1);

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
             * @var PlanResource|null $plan
             * @example {"id": 1, "name": "Basic Plan", "price": 9.99, "features": ["unlimited_products"]}
             */
            'plan' => $this->whenLoaded('plan', fn () => new PlanResource($this->plan)),

            /**
             * The trial period end date.
             * @var string|null $trial_ends_at
             * @example "2026-06-16 23:59:59"
             */
            'trial_ends_at' => $this->trial_ends_at?->toDateTimeString(),

            /**
             * The tenant associated domains.
             * @var AnonymousResourceCollection|null $domains
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
