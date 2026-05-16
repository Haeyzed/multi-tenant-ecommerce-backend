<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Enums\Central\TenantStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            // Tenant details
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'domain' => ['required', 'string', 'unique:domains,domain', 'regex:/^[a-z0-9-]+$/'],
            'status' => ['nullable', Rule::enum(TenantStatus::class)],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'data' => ['nullable', 'array'],

            // Admin user details
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:tenants,email'],
            'admin_password' => ['nullable', Password::defaults()],
            'admin_phone' => ['nullable', 'string', 'max:20'],
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
