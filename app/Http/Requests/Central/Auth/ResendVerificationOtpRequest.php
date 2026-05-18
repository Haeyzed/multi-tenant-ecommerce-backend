<?php

declare(strict_types=1);

namespace App\Http\Requests\Central\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ResendVerificationOtpRequest
 *
 * Validates email resend with OTP.
 *
 * @package App\Http\Requests\Central\Auth
 */
class ResendVerificationOtpRequest extends FormRequest
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
             * The user's email.
             * @var string $email
             * @example "user@platform.com"
             */
            'email' => ['required', 'email'],
        ];
    }
}
