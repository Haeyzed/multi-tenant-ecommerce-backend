<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserRequest
 *
 * Validates central platform user creation.
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            /**
             * The role to assign to the user.
             * @var string|null $role
             * @example "super_admin"
             */
            'role' => ['nullable', 'string', 'exists:roles,name'],

            /**
             * Whether the user account is active.
             * @var bool|null $is_active
             * @example true
             */
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
