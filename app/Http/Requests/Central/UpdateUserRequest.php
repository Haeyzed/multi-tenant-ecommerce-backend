<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Class UpdateUserRequest
 *
 * Validates central user profile updates.
 * Allows partial updates with optional password change.
 *
 * @package App\Http\Requests\Central
 */
class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            /**
             * The updated full name.
             * @var string|null $name
             * @example "Jane Doe"
             */
            'name' => ['sometimes', 'string', 'max:255'],

            /**
             * The updated email address.
             * @var string|null $email
             * @example "jane.doe@platform.com"
             */
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],

            /**
             * The updated phone number.
             * @var string|null $phone
             * @example "+234 800 333 4444"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The new password (requires confirmation).
             * @var string|null $password
             * @example "NewSecureP@ss456!"
             */
            'password' => ['nullable', Password::defaults(), 'confirmed'],

            /**
             * The updated role assignment.
             * @var string|null $role
             * @example "billing_manager"
             */
            'role' => ['nullable', 'string', 'exists:roles,name'],

            /**
             * The updated account status.
             * @var bool|null $is_active
             * @example false
             */
            'is_active' => ['nullable', 'boolean'],

            /**
             * Direct permissions to sync (replaces existing direct permissions).
             * @var array<string>|null $permissions
             * @example ["invoices.manage", "reports.view"]
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
