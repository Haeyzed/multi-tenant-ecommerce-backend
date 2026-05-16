<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSettingRequest
 *
 * Validates tenant settings update requests.
 *
 * @package App\Http\Requests\Tenant
 */
class UpdateSettingRequest extends FormRequest
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
            // Branding
            'store_name' => ['sometimes', 'string', 'max:255'],
            'store_tagline' => ['sometimes', 'string', 'max:500', 'nullable'],
            'store_logo_url' => ['sometimes', 'url', 'nullable'],
            'store_logo_dark_url' => ['sometimes', 'url', 'nullable'],
            'store_favicon_url' => ['sometimes', 'url', 'nullable'],
            'primary_color' => ['sometimes', 'string', 'regex:/^#[a-fA-F0-9]{6}$/', 'nullable'],
            'accent_color' => ['sometimes', 'string', 'regex:/^#[a-fA-F0-9]{6}$/', 'nullable'],
            'secondary_color' => ['sometimes', 'string', 'regex:/^#[a-fA-F0-9]{6}$/', 'nullable'],

            // Contact
            'store_email' => ['sometimes', 'email', 'nullable'],
            'store_phone' => ['sometimes', 'string', 'max:20', 'nullable'],
            'store_whatsapp' => ['sometimes', 'string', 'max:20', 'nullable'],
            'store_address' => ['sometimes', 'string', 'nullable'],
            'store_website' => ['sometimes', 'url', 'nullable'],

            // Commerce
            'store_active' => ['sometimes', 'boolean'],
            'catalog_visible' => ['sometimes', 'boolean'],
            'checkout_enabled' => ['sometimes', 'boolean'],
            'guest_checkout_allowed' => ['sometimes', 'boolean'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'currency_symbol' => ['sometimes', 'string', 'max:5'],
            'currency_position' => ['sometimes', 'in:before,after'],
            'tax_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'prices_include_tax' => ['sometimes', 'boolean'],
            'minimum_order_amount' => ['sometimes', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['sometimes', 'numeric', 'min:0', 'nullable'],

            // Inventory
            'inventory_tracking_enabled' => ['sometimes', 'boolean'],
            'low_stock_alerts_enabled' => ['sometimes', 'boolean'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:1'],

            // Shipping
            'shipping_enabled' => ['sometimes', 'boolean'],
            'local_pickup_enabled' => ['sometimes', 'boolean'],
            'default_weight_unit' => ['sometimes', 'in:kg,g,lb,oz'],
            'default_dimension_unit' => ['sometimes', 'in:cm,m,in,ft'],
            'shipping_zones' => ['sometimes', 'array', 'nullable'],

            // Payments
            'cod_enabled' => ['sometimes', 'boolean'],
            'card_payment_enabled' => ['sometimes', 'boolean'],
            'bank_transfer_enabled' => ['sometimes', 'boolean'],
            'wallet_payment_enabled' => ['sometimes', 'boolean'],
            'payment_gateways' => ['sometimes', 'array', 'nullable'],
            'paystack_public_key' => ['sometimes', 'string', 'nullable'],
            'paystack_secret_key' => ['sometimes', 'string', 'nullable'],
            'flutterwave_public_key' => ['sometimes', 'string', 'nullable'],
            'flutterwave_secret_key' => ['sometimes', 'string', 'nullable'],

            // Orders
            'order_prefix' => ['sometimes', 'string', 'max:10'],
            'order_start_number' => ['sometimes', 'integer', 'min:1'],
            'auto_confirm_orders' => ['sometimes', 'boolean'],
            'auto_invoice_on_ship' => ['sometimes', 'boolean'],
            'order_cancellation_window_hours' => ['sometimes', 'integer', 'min:0'],

            // Customers
            'customer_registration_required' => ['sometimes', 'boolean'],
            'customer_verification_required' => ['sometimes', 'boolean'],
            'loyalty_program_enabled' => ['sometimes', 'boolean'],

            // Notifications
            'email_order_confirmation' => ['sometimes', 'boolean'],
            'email_shipping_updates' => ['sometimes', 'boolean'],
            'sms_order_updates' => ['sometimes', 'boolean'],
            'push_notifications_enabled' => ['sometimes', 'boolean'],
            'notification_email_from' => ['sometimes', 'email', 'nullable'],
            'notification_email_from_name' => ['sometimes', 'string', 'max:255', 'nullable'],

            // SEO
            'meta_title' => ['sometimes', 'string', 'max:255', 'nullable'],
            'meta_description' => ['sometimes', 'string', 'max:500', 'nullable'],
            'meta_keywords' => ['sometimes', 'string', 'max:500', 'nullable'],

            // Social
            'facebook_url' => ['sometimes', 'url', 'nullable'],
            'instagram_url' => ['sometimes', 'url', 'nullable'],
            'twitter_url' => ['sometimes', 'url', 'nullable'],
            'linkedin_url' => ['sometimes', 'url', 'nullable'],
            'youtube_url' => ['sometimes', 'url', 'nullable'],
            'tiktok_url' => ['sometimes', 'url', 'nullable'],

            // Legal
            'terms_and_conditions' => ['sometimes', 'string', 'nullable'],
            'privacy_policy' => ['sometimes', 'string', 'nullable'],
            'refund_policy' => ['sometimes', 'string', 'nullable'],
            'shipping_policy' => ['sometimes', 'string', 'nullable'],

            // Localization
            'language' => ['sometimes', 'string', 'size:2'],
            'timezone' => ['sometimes', 'string'],
            'date_format' => ['sometimes', 'string'],
            'time_format' => ['sometimes', 'string'],
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
            'store_name' => 'store name',
            'store_email' => 'store email',
            'store_phone' => 'store phone',
            'primary_color' => 'primary color',
            'accent_color' => 'accent color',
            'tax_rate' => 'tax rate',
            'minimum_order_amount' => 'minimum order amount',
            'low_stock_threshold' => 'low stock threshold',
            'order_prefix' => 'order prefix',
            'paystack_public_key' => 'Paystack public key',
            'paystack_secret_key' => 'Paystack secret key',
            'flutterwave_public_key' => 'Flutterwave public key',
            'flutterwave_secret_key' => 'Flutterwave secret key',
        ];
    }
}
