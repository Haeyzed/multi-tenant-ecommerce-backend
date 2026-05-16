<?php

declare(strict_types=1);

namespace App\Http\Requests\Central\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 *
 * Validates user login credentials.
 *
 * @package App\Http\Requests\Central\Auth
 */
class LoginRequest extends FormRequest
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
             * The user's email address.
             * @var string $email
             * @example "superadmin@platform.com"
             */
            'email' => ['required', 'email'],

            /**
             * The user's password.
             * @var string $password
             * @example "password"
             */
            'password' => ['required', 'string'],

            /**
             * Whether to remember the session.
             * @var bool|null $remember
             * @example true
             */
            'remember' => ['nullable', 'boolean'],
        ];
    }
}
