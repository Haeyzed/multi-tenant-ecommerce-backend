<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Enums\Central\TenantStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Class CreateTenantRequest
 *
 * Validates tenant onboarding with admin user creation.
 *
 * @package App\Http\Requests\Central
 */
class CreateTenantRequest extends FormRequest
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
            // === TENANT DETAILS ===

            /**
             * The business/organization name.
             * @var string $name
             * @example "Green Mart Nigeria"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The tenant business email address.
             * @var string $email
             * @example "info@greenmart.ng"
             */
            'email' => ['required', 'email', 'unique:tenants,email'],

            /**
             * The tenant business phone number.
             * @var string|null $phone
             * @example "+234 800 123 4567"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The subdomain for tenant access.
             * @var string $domain
             * @example "greenmart"
             */
            'domain' => ['required', 'string', 'unique:domains,domain', 'regex:/^[a-z0-9-]+$/'],

            /**
             * The tenant account status.
             * @var string|null $status
             * @example "active"
             */
            'status' => ['nullable', Rule::enum(TenantStatus::class)],

            /**
             * The subscription plan ID.
             * @var int $plan_id
             * @example 2
             */
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('plans', 'id')->where('is_active', true),
            ],

            /**
             * Additional tenant metadata.
             * @var array<string, mixed>|null $data
             * @example {"industry": "retail", "referral_code": "REF123"}
             */
            'data' => ['nullable', 'array'],

            // === ADMIN USER DETAILS ===

            /**
             * The admin user's full name.
             * @var string $admin_name
             * @example "John Doe"
             */
            'admin_name' => ['required', 'string', 'max:255'],

            /**
             * The admin user's email for login.
             * @var string $admin_email
             * @example "john@greenmart.ng"
             */
            'admin_email' => ['required', 'email', 'different:email'],

            /**
             * The admin user's password (auto-generated if null).
             * @var string|null $admin_password
             * @example "SecureP@ss123!"
             */
            'admin_password' => ['nullable', Password::defaults()],

            /**
             * The admin user's phone number.
             * @var string|null $admin_phone
             * @example "+234 800 987 6543"
             */
            'admin_phone' => ['nullable', 'string', 'max:20'],

            ...TenantStoreSettingsRules::rules(),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'tenant name',
            'email' => 'tenant email',
            'domain' => 'tenant domain',
            'plan_id' => 'subscription plan',
            'admin_name' => 'admin name',
            'admin_email' => 'admin email',
            'admin_password' => 'admin password',
            'admin_phone' => 'admin phone',
            ...TenantStoreSettingsRules::attributes(),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('domain')) {
            $this->merge([
                'domain' => strtolower(trim($this->domain)),
            ]);
        }
    }
}
