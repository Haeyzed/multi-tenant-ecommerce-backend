<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class ChangePasswordRequest
 *
 * Validates authenticated password change.
 *
 * @package App\Http\Requests\Tenant\Auth
 */
class ChangePasswordRequest extends FormRequest
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
             * The current password.
             * @var string $current_password
             * @example "OldP@ss123!"
             */
            'current_password' => ['required', 'string'],

            /**
             * The new password.
             * @var string $new_password
             * @example "NewSecureP@ss456!"
             */
            'new_password' => ['required', Password::defaults(), 'confirmed'],
        ];
    }
}
