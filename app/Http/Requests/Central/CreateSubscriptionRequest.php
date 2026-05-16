<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateSubscriptionRequest
 *
 * Validates subscription creation with payment processing.
 *
 * @package App\Http\Requests\Central
 */
class CreateSubscriptionRequest extends FormRequest
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
             * The subscription plan ID.
             * @var int $plan_id
             * @example 2
             */
            'plan_id' => ['required', 'exists:plans,id'],

            /**
             * The payment gateway to process the transaction.
             * @var string $payment_gateway
             * @example "paystack"
             */
            'payment_gateway' => ['required', 'string', 'in:paystack,flutterwave,stripe,cash_on_delivery'],

            /**
             * The external transaction reference ID.
             * @var string|null $transaction_id
             * @example "TRX_1234567890"
             */
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
