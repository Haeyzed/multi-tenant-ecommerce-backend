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
        $tenantId = $this->route('tenant');
        $primaryDomainId = null;

        if ($tenantId) {
            $primaryDomainId = \App\Models\Central\Domain::query()
                ->where('tenant_id', $tenantId)
                ->where('is_primary', true)
                ->value('id');
        }

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('tenants', 'email')->ignore($tenantId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'domain' => [
                'sometimes',
                'string',
                Rule::unique('domains', 'domain')->ignore($primaryDomainId),
                'regex:/^[a-z0-9-]+(\.[a-z0-9.-]+)*$/i',
            ],
            'status' => ['sometimes', Rule::enum(TenantStatus::class)],
            'plan_id' => [
                'sometimes',
                'integer',
                Rule::exists('plans', 'id')->where('is_active', true),
            ],
            'data' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('domain')) {
            $this->merge([
                'domain' => strtolower(trim((string) $this->domain)),
            ]);
        }
    }
}
