<?php

namespace App\Http\Requests;

use App\Enums\TenantStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTenantRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'domain' => ['required', 'string', 'unique:domains,domain', 'regex:/^[a-z0-9-]+$/'],
            'status' => ['nullable', Rule::enum(TenantStatus::class)],
            'plan' => ['nullable', 'string', 'in:basic,premium,enterprise'],
            'data' => ['nullable', 'array'],
        ];
    }
}
