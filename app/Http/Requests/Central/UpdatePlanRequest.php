<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:plans,name,' . $this->route('plan')->id],
            'price' => ['required', 'numeric', 'min:0'],
            'features' => ['nullable', 'array'],
            'limits' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ];
    }
}
