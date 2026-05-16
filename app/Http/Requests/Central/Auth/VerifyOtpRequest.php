<?php

declare(strict_types=1);

namespace App\Http\Requests\Central\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class VerifyOtpRequest
 *
 * Validates OTP verification data.
 *
 * @package App\Http\Requests\Central\Auth
 */
class VerifyOtpRequest extends FormRequest
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

            /**
             * The 6-digit OTP code.
             * @var string $otp
             * @example "123456"
             */
            'otp' => ['required', 'string', 'size:6'],

            /**
             * The verification type.
             * @var string $type
             * @example "email_verification"
             */
            'type' => ['required', 'string', 'in:email_verification,password_reset'],
        ];
    }
}
