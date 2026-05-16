<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Enums\Central\TenantStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateTenantRequest
 *
 * Validates tenant profile updates.
 *
 * @package App\Http\Requests\Central
 */
class UpdateTenantRequest extends FormRequest
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
             * The updated business name.
             * @var string|null $name
             * @example "Green Mart Nigeria Ltd"
             */
            'name' => ['sometimes', 'string', 'max:255'],

            /**
             * The updated business email.
             * @var string|null $email
             * @example "newemail@greenmart.ng"
             */
            'email' => ['sometimes', 'email', Rule::unique('tenants', 'email')->ignore($this->route('tenant'))],

            /**
             * The updated business phone.
             * @var string|null $phone
             * @example "+234 800 111 2222"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The updated subdomain.
             * @var string|null $domain
             * @example "greenmart-new"
             */
            'domain' => ['sometimes', 'string', Rule::unique('domains', 'domain')->ignore($this->route('tenant'), 'tenant_id'), 'regex:/^[a-z0-9-]+$/'],

            /**
             * The updated account status.
             * @var string|null $status
             * @example "suspended"
             */
            'status' => ['sometimes', Rule::enum(TenantStatus::class)],

            /**
             * The updated subscription plan.
             * @var string|null $plan
             * @example "premium"
             */
            'plan' => ['sometimes', 'string', 'in:basic,premium,enterprise'],

            /**
             * Updated metadata.
             * @var array<string, mixed>|null $data
             * @example {"industry": "wholesale"}
             */
            'data' => ['nullable', 'array'],
        ];
    }
}
