<?php

declare(strict_types=1);

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class DomainResource
 *
 * Transforms domain model into API response format.
 *
 * @package App\Http\Resources\Central
 */
class DomainResource extends JsonResource
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
             * The domain unique identifier.
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The domain name/subdomain.
             * @var string $domain
             * @example "greenmart.localhost"
             */
            'domain' => $this->domain,

            /**
             * Whether this is the primary domain for the tenant.
             * @var bool $is_primary
             * @example true
             */
            'is_primary' => $this->is_primary,

            /**
             * The domain creation timestamp.
             * @var string $created_at
             * @example "2026-05-16 14:30:00"
             */
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
