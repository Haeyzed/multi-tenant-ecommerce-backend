<?php

declare(strict_types=1);

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * Transforms central user model into API response format.
 *
 * @package App\Http\Resources\Central
 */
class UserResource extends JsonResource
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
             * The user unique identifier.
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The user's full name.
             * @var string $name
             * @example "Jane Smith"
             */
            'name' => $this->name,

            /**
             * The user's email address.
             * @var string $email
             * @example "jane.smith@platform.com"
             */
            'email' => $this->email,

            /**
             * The user's phone number.
             * @var string|null $phone
             * @example "+234 800 555 0199"
             */
            'phone' => $this->phone,

            /**
             * Whether the user account is active.
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The email verification timestamp.
             * @var string|null $email_verified_at
             * @example "2026-05-16 12:00:00"
             */
            'email_verified_at' => $this->email_verified_at?->toDateTimeString(),

            /**
             * The assigned role names.
             * @var array<string>|null $roles
             * @example ["super_admin", "billing_manager"]
             */
            'roles' => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),

            /**
             * The direct permission names.
             * @var array<string>|null $permissions
             * @example ["tenants.manage", "plans.create"]
             */
            'permissions' => $this->whenLoaded('permissions', fn() => $this->permissions->pluck('name')),

            /**
             * The user creation timestamp.
             * @var string $created_at
             * @example "2026-05-10 08:00:00"
             */
            'created_at' => $this->created_at->toDateTimeString(),

            /**
             * The user last update timestamp.
             * @var string $updated_at
             * @example "2026-05-16 15:20:00"
             */
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
