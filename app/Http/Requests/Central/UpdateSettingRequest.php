<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_name' => ['sometimes', 'string', 'max:255'],
            'site_logo_url' => ['sometimes', 'nullable', 'string', 'max:2048', 'url'],
            'primary_color' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'accent_color' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'secondary_color' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'support_email' => ['sometimes', 'email', 'max:255'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'maintenance_mode' => ['sometimes', 'boolean'],
            'trial_days' => ['sometimes', 'integer', 'min:0', 'max:365'],
            'default_plan_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('plans', 'id')->where('is_active', true),
            ],
            'email_notifications' => ['sometimes', 'boolean'],
            'sms_notifications' => ['sometimes', 'boolean'],
        ];
    }
}
