<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 *
 * Validates user authentication credentials.
 *
 * @package App\Http\Requests\Central
 */
class LoginRequest extends FormRequest
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
             * The user's email address.
             * @var string $email
             * @example "admin@platform.com"
             */
            'email' => ['required', 'email'],

            /**
             * The user's password.
             * @var string $password
             * @example "SecureP@ss123!"
             */
            'password' => ['required', 'string'],
        ];
    }
}
