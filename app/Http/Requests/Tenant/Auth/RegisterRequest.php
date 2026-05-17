<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class RegisterRequest
 *
 * Validates user registration data.
 *
 * @package App\Http\Requests\Tenant\Auth
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules.
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
             * @example "jane@platform.com"
             */
            'email' => ['required', 'email', 'unique:users,email'],

            /**
             * The user's phone number.
             * @var string|null $phone
             * @example "+234 800 555 0199"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The user's password.
             * @var string $password
             * @example "SecureP@ss123!"
             */
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];
    }
}
