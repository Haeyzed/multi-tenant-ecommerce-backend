<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class CreateUserRequest
 *
 * Validates central platform user creation data.
 * Supports role assignment and direct permissions.
 *
 * @package App\Http\Requests\Central
 */
class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The user's full name.
             * @var string $name
             * @example "Jane Smith"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The user's email address.
             * @var string $email
             * @example "jane.smith@platform.com"
             */
            'email' => ['required', 'email', 'unique:users,email'],

            /**
             * The user's phone number.
             * @var string|null $phone
             * @example "+234 800 555 0199"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The user's password with confirmation.
             * @var string $password
             * @example "SecureP@ss123!"
             */
            'password' => ['required', Password::defaults(), 'confirmed'],

            /**
             * The Spatie role to assign.
             * @var string|null $role
             * @example "admin"
             */
            'role' => ['nullable', 'string', 'exists:roles,name'],

            /**
             * Whether the user account is active.
             * @var bool|null $is_active
             * @example true
             */
            'is_active' => ['nullable', 'boolean'],

            /**
             * Direct permissions to assign beyond role permissions.
             * @var array<string>|null $permissions
             * @example ["tenants.manage", "plans.create"]
             */
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'password' => 'password',
            'role' => 'user role',
            'is_active' => 'account status',
            'permissions' => 'permissions',
            'permissions.*' => 'permission',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }
    }
}
