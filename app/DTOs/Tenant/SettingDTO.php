<?php

declare(strict_types=1);

namespace App\DTOs\Tenant;

use Illuminate\Http\Request;

/**
 * Class SettingDTO
 *
 * Data transfer object for tenant settings updates.
 *
 * @package App\DTOs\Tenant
 */
readonly class SettingDTO
{
    /**
     * Create a new SettingDTO instance.
     *
     * @param array<string, mixed> $settings Key-value settings to update
     */
    public function __construct(
        public array $settings
    ) {}

    /**
     * Create a SettingDTO from a validated request.
     *
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            settings: $request->validated()
        );
    }

    /**
     * Create a SettingDTO from an array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            settings: $data
        );
    }

    /**
     * Convert to array for repository update.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->settings;
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Check if a key exists in the settings.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->settings);
    }

    /**
     * Get only branding-related settings.
     *
     * @return array<string, mixed>
     */
    public function brandingOnly(): array
    {
        $keys = [
            'store_name',
            'store_tagline',
            'store_logo_url',
            'store_logo_dark_url',
            'store_favicon_url',
            'primary_color',
            'accent_color',
            'secondary_color',
        ];

        return array_intersect_key($this->settings, array_flip($keys));
    }

    /**
     * Get only payment-related settings.
     *
     * @return array<string, mixed>
     */
    public function paymentsOnly(): array
    {
        $keys = [
            'cod_enabled',
            'card_payment_enabled',
            'bank_transfer_enabled',
            'wallet_payment_enabled',
            'payment_gateways',
            'paystack_public_key',
            'paystack_secret_key',
            'flutterwave_public_key',
            'flutterwave_secret_key',
        ];

        return array_intersect_key($this->settings, array_flip($keys));
    }
}
