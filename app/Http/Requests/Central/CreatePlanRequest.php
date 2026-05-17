<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreatePlanRequest
 *
 * Validates data for creating a new subscription plan.
 *
 * @package App\Http\Requests\Central
 */
class CreatePlanRequest extends FormRequest
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
             * The plan name.
             * @var string $name
             * @example "Premium Plan"
             */
            'name' => ['required', 'string', 'max:255', 'unique:plans,name'],

            /**
             * The monthly subscription price.
             * @var float $price
             * @example 49.99
             */
            'price' => ['required', 'numeric', 'min:0'],

            /**
             * List of features included in the plan.
             * @var array<string> $features
             * @example ["unlimited_products", "advanced_analytics", "priority_support"]
             */
            'features' => ['nullable', 'array'],
            'features.*' => ['string', Rule::in(array_keys(config('plan_modules.modules', [])))],

            /**
             * Usage limits for the plan.
             * @var array<string, int> $limits
             * @example {"products": 100, "staff": 5, "storage_gb": 10}
             */
            'limits' => ['nullable', 'array'],
            'limits.*' => ['numeric'],

            /**
             * Whether the plan is available for subscription.
             * @var bool $is_active
             * @example true
             */
            'is_active' => ['boolean'],
        ];
    }
}
