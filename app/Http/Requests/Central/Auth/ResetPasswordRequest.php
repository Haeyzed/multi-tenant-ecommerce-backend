<?php

declare(strict_types=1);

namespace App\Http\Requests\Central\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class ResetPasswordRequest
 *
 * Validates password reset with OTP.
 *
 * @package App\Http\Requests\Central\Auth
 */
class ResetPasswordRequest extends FormRequest
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
             * Token from verify-otp (password_reset). Use this after verify-otp, or send otp instead for a one-step reset.
             * @var string|null $reset_token
             */
            'reset_token' => ['required_without:otp', 'string', 'size:64'],

            /**
             * OTP from forgot-password email. Alternative to reset_token when skipping verify-otp.
             * @var string|null $otp
             * @example "123456"
             */
            'otp' => ['required_without:reset_token', 'string', 'size:6'],

            /**
             * The new password.
             * @var string $password
             * @example "NewSecureP@ss456!"
             */
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];
    }
}
