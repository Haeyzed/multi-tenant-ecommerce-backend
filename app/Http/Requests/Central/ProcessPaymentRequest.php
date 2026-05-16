<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProcessPaymentRequest
 *
 * Validates payment processing for subscriptions or invoices.
 *
 * @package App\Http\Requests\Central
 */
class ProcessPaymentRequest extends FormRequest
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
             * The payment method to use.
             * @var string $payment_method
             * @example "paystack"
             */
            'payment_method' => ['required', 'string', 'in:paystack,flutterwave,stripe,cash_on_delivery'],

            /**
             * The external transaction reference.
             * @var string|null $transaction_id
             * @example "TRX_9876543210"
             */
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
