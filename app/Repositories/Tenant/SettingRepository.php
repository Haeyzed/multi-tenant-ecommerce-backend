<?php

declare(strict_types=1);

namespace App\Repositories\Tenant;

use App\Models\Tenant\Setting;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class SettingRepository
 *
 * Handles data access for tenant settings.
 *
 * @package App\Repositories\Tenant
 */
class SettingRepository
{
    /**
     * Get the settings instance.
     *
     * @return Setting
     */
    public function getSettings(): Setting
    {
        return Setting::instance();
    }

    /**
     * Update settings.
     *
     * @param array<string, mixed> $data
     * @return Setting
     */
    public function update(array $data): Setting
    {
        return Setting::updateSettings($data);
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * Get store branding information.
     *
     * @return array<string, mixed>
     */
    public function getBranding(): array
    {
        return [
            'store_name' => Setting::storeName(),
            'store_tagline' => Setting::get('store_tagline'),
            'logo_url' => Setting::get('store_logo_url'),
            'logo_dark_url' => Setting::get('store_logo_dark_url'),
            'favicon_url' => Setting::get('store_favicon_url'),
            'colors' => Setting::brandColors(),
        ];
    }

    /**
     * Get commerce configuration.
     *
     * @return array<string, mixed>
     */
    public function getCommerceConfig(): array
    {
        return [
            'currency' => Setting::currencyConfig(),
            'tax_rate' => Setting::get('tax_rate', 0),
            'prices_include_tax' => Setting::get('prices_include_tax', false),
            'minimum_order_amount' => Setting::get('minimum_order_amount', 0),
            'free_shipping_threshold' => Setting::get('free_shipping_threshold'),
        ];
    }

    /**
     * Get payment configuration.
     *
     * @return array<string, mixed>
     */
    public function getPaymentConfig(): array
    {
        return [
            'methods' => Setting::activePaymentMethods(),
            'gateways' => Setting::get('payment_gateways', []),
            'paystack' => [
                'public_key' => Setting::get('paystack_public_key'),
            ],
            'flutterwave' => [
                'public_key' => Setting::get('flutterwave_public_key'),
            ],
        ];
    }
}
