<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUserRequest
 *
 * Validates central user profile updates.
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
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($this->route('user'))],

            /**
             * The updated phone number.
             * @var string|null $phone
             * @example "+234 800 333 4444"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The new password with confirmation.
             * @var string|null $password
             * @example "NewSecureP@ss456!"
             */
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            /**
             * The updated role assignment.
             * @var string|null $role
             * @example "admin"
             */
            'role' => ['nullable', 'string', 'exists:roles,name'],

            /**
             * The updated account status.
             * @var bool|null $is_active
             * @example false
             */
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
