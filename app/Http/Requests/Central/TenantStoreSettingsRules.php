<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

/**
 * Validation rules for tenant store settings during central tenant onboarding.
 */
final class TenantStoreSettingsRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'settings' => ['nullable', 'array'],
            'settings.store_name' => ['sometimes', 'string', 'max:255'],
            'settings.store_tagline' => ['nullable', 'string', 'max:500'],
            'settings.store_logo_url' => ['nullable', 'url', 'max:2048'],
            'settings.store_logo_dark_url' => ['nullable', 'url', 'max:2048'],
            'settings.store_favicon_url' => ['nullable', 'url', 'max:2048'],
            'settings.primary_color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'settings.accent_color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'settings.secondary_color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'settings.store_email' => ['nullable', 'email', 'max:255'],
            'settings.store_phone' => ['nullable', 'string', 'max:20'],
            'settings.store_whatsapp' => ['nullable', 'string', 'max:20'],
            'settings.store_address' => ['nullable', 'string', 'max:1000'],
            'settings.store_website' => ['nullable', 'url', 'max:2048'],
            'settings.industry' => ['nullable', 'string', 'max:100'],
            'settings.business_type' => ['nullable', 'string', 'max:100'],
            'settings.currency' => ['sometimes', 'string', 'size:3'],
            'settings.currency_symbol' => ['sometimes', 'string', 'max:5'],
            'settings.currency_position' => ['sometimes', 'in:before,after'],
            'settings.tax_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'settings.timezone' => ['sometimes', 'string', 'timezone'],
            'settings.language' => ['sometimes', 'string', 'max:10'],
            'settings.meta_title' => ['nullable', 'string', 'max:255'],
            'settings.meta_description' => ['nullable', 'string', 'max:500'],
            'settings.notification_email_from_name' => ['nullable', 'string', 'max:255'],
            'settings.store_active' => ['sometimes', 'boolean'],
            'settings.catalog_visible' => ['sometimes', 'boolean'],
            'settings.checkout_enabled' => ['sometimes', 'boolean'],
            'settings.guest_checkout_allowed' => ['sometimes', 'boolean'],
            'settings.shipping_enabled' => ['sometimes', 'boolean'],
            'settings.cod_enabled' => ['sometimes', 'boolean'],
            'settings.card_payment_enabled' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'settings' => 'store settings',
            'settings.store_name' => 'store name',
            'settings.store_tagline' => 'store tagline',
            'settings.store_logo_url' => 'store logo URL',
            'settings.primary_color' => 'primary brand color',
            'settings.accent_color' => 'accent brand color',
            'settings.store_email' => 'store contact email',
            'settings.store_phone' => 'store phone',
            'settings.currency' => 'currency',
            'settings.timezone' => 'timezone',
        ];
    }
}
