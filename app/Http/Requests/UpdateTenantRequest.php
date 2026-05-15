<?php

namespace App\Http\Requests;

use App\Enums\TenantStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('tenants', 'email')->ignore($this->route('tenant'))],
            'phone' => ['nullable', 'string', 'max:20'],
            'domain' => ['sometimes', 'string', Rule::unique('domains', 'domain')->ignore($this->route('tenant'), 'tenant_id'), 'regex:/^[a-z0-9-]+$/'],
            'status' => ['sometimes', Rule::enum(TenantStatus::class)],
            'plan' => ['sometimes', 'string', 'in:basic,premium,enterprise'],
            'data' => ['nullable', 'array'],
        ];
    }
}
