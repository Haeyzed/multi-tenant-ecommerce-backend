<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AssignRoleRequest
 *
 * Validates role assignment to users.
 *
 * @package App\Http\Requests\Central
 */
class AssignRoleRequest extends FormRequest
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
             * The role name to assign.
             * @var string $role
             * @example "admin"
             */
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
