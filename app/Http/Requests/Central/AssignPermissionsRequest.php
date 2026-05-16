<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AssignPermissionsRequest
 *
 * Validates direct permission assignments to users.
 *
 * @package App\Http\Requests\Central
 */
class AssignPermissionsRequest extends FormRequest
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
             * List of permission names to assign.
             * @var array<string> $permissions
             * @example ["tenants.manage", "plans.create", "users.view"]
             */
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', 'exists:permissions,name'],
        ];
    }

    /**
     * Get custom attributes.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'permissions' => 'permissions list',
            'permissions.*' => 'permission name',
        ];
    }
}
