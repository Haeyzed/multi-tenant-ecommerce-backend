<?php

declare(strict_types=1);

namespace App\Http\Requests\Central\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ForgotPasswordRequest
 *
 * Validates password reset request.
 *
 * @package App\Http\Requests\Central\Auth
 */
class ForgotPasswordRequest extends FormRequest
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
             * The registered email address.
             * @var string $email
             * @example "user@platform.com"
             */
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }
}
