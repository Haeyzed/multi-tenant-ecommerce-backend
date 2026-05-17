<?php

declare(strict_types=1);

namespace App\DTOs\Central;

/**
 * Store settings applied to the tenant database during onboarding.
 */
readonly class TenantStoreSettingsDTO
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public array $attributes = [],
    ) {}

    /**
     * @param array<string, mixed> $settings Validated `settings` payload (may be empty)
     */
    public static function fromRequest(
        array $settings,
        string $tenantName,
        string $tenantEmail,
        ?string $tenantPhone,
    ): self {
        $attributes = [
            'store_name' => $settings['store_name'] ?? $tenantName,
            'store_tagline' => $settings['store_tagline'] ?? null,
            'store_logo_url' => $settings['store_logo_url'] ?? null,
            'store_logo_dark_url' => $settings['store_logo_dark_url'] ?? null,
            'store_favicon_url' => $settings['store_favicon_url'] ?? null,
            'primary_color' => $settings['primary_color'] ?? null,
            'accent_color' => $settings['accent_color'] ?? null,
            'secondary_color' => $settings['secondary_color'] ?? null,
            'store_email' => $settings['store_email'] ?? $tenantEmail,
            'store_phone' => $settings['store_phone'] ?? $tenantPhone,
            'store_whatsapp' => $settings['store_whatsapp'] ?? null,
            'store_address' => $settings['store_address'] ?? null,
            'store_website' => $settings['store_website'] ?? null,
            'industry' => $settings['industry'] ?? null,
            'business_type' => $settings['business_type'] ?? null,
            'currency' => $settings['currency'] ?? 'NGN',
            'currency_symbol' => $settings['currency_symbol'] ?? '₦',
            'currency_position' => $settings['currency_position'] ?? 'before',
            'tax_rate' => $settings['tax_rate'] ?? 0.0,
            'timezone' => $settings['timezone'] ?? 'Africa/Lagos',
            'language' => $settings['language'] ?? 'en',
            'meta_title' => $settings['meta_title'] ?? ($settings['store_name'] ?? $tenantName),
            'meta_description' => $settings['meta_description'] ?? null,
            'notification_email_from_name' => $settings['notification_email_from_name']
                ?? ($settings['store_name'] ?? $tenantName),
        ];

        if (array_key_exists('store_active', $settings)) {
            $attributes['store_active'] = (bool) $settings['store_active'];
        }

        if (array_key_exists('catalog_visible', $settings)) {
            $attributes['catalog_visible'] = (bool) $settings['catalog_visible'];
        }

        if (array_key_exists('checkout_enabled', $settings)) {
            $attributes['checkout_enabled'] = (bool) $settings['checkout_enabled'];
        }

        if (array_key_exists('guest_checkout_allowed', $settings)) {
            $attributes['guest_checkout_allowed'] = (bool) $settings['guest_checkout_allowed'];
        }

        if (array_key_exists('shipping_enabled', $settings)) {
            $attributes['shipping_enabled'] = (bool) $settings['shipping_enabled'];
        }

        if (array_key_exists('cod_enabled', $settings)) {
            $attributes['cod_enabled'] = (bool) $settings['cod_enabled'];
        }

        if (array_key_exists('card_payment_enabled', $settings)) {
            $attributes['card_payment_enabled'] = (bool) $settings['card_payment_enabled'];
        }

        return new self($attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
