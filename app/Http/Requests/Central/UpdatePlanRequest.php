<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePlanRequest
 *
 * Validates subscription plan updates.
 *
 * @package App\Http\Requests\Central
 */
class UpdatePlanRequest extends FormRequest
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
             * The updated plan name.
             * @var string $name
             * @example "Enterprise Plan"
             */
            'name' => ['required', 'string', 'max:255', 'unique:plans,name,' . $this->route('plan')->id],

            /**
             * The updated monthly price.
             * @var float $price
             * @example 99.99
             */
            'price' => ['required', 'numeric', 'min:0'],

            /**
             * Updated feature list.
             * @var array<string>|null $features
             * @example ["unlimited_products", "white_label", "api_access"]
             */
            'features' => ['nullable', 'array'],

            /**
             * Updated usage limits.
             * @var array<string, int>|null $limits
             * @example {"products": -1, "staff": 50, "storage_gb": 100}
             */
            'limits' => ['nullable', 'array'],

            /**
             * Updated availability status.
             * @var bool|null $is_active
             * @example true
             */
            'is_active' => ['boolean'],
        ];
    }
}
