<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class Setting
 *
 * Tenant-specific e-commerce settings are stored as a singleton record.
 * Each tenant database contains exactly one settings row that controls
 * store behavior, branding, payments, shipping, and notifications.
 *
 * @property int $id
 * @property string $store_name
 * @property string|null $store_tagline
 * @property string|null $store_logo_url
 * @property string|null $store_favicon_url
 * @property string|null $primary_color
 * @property string|null $accent_color
 * @property string|null $secondary_color
 * @property string|null $store_email
 * @property string|null $store_phone
 * @property string|null $store_address
 * @property bool $store_active
 * @property bool $catalog_visible
 * @property bool $checkout_enabled
 * @property bool $guest_checkout_allowed
 * @property string $currency
 * @property string $currency_symbol
 * @property string $currency_position
 * @property float $tax_rate
 * @property bool $prices_include_tax
 * @property bool $inventory_tracking_enabled
 * @property int $low_stock_threshold
 * @property bool $shipping_enabled
 * @property bool $cod_enabled
 * @property bool $card_payment_enabled
 * @property bool $bank_transfer_enabled
 * @property bool $wallet_payment_enabled
 * @property string|null $paystack_public_key
 * @property string|null $paystack_secret_key
 * @property string|null $flutterwave_public_key
 * @property string|null $flutterwave_secret_key
 * @property string $order_prefix
 * @property int $order_start_number
 * @property bool $auto_confirm_orders
 * @property bool $email_order_confirmation
 * @property bool $customer_registration_required
 * @property bool $loyalty_program_enabled
 * @property string $language
 * @property string $timezone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass-assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'store_active' => 'boolean',
        'catalog_visible' => 'boolean',
        'checkout_enabled' => 'boolean',
        'guest_checkout_allowed' => 'boolean',
        'wishlist_enabled' => 'boolean',
        'reviews_enabled' => 'boolean',
        'compare_products_enabled' => 'boolean',
        'inventory_tracking_enabled' => 'boolean',
        'low_stock_alerts_enabled' => 'boolean',
        'prices_include_tax' => 'boolean',
        'show_tax_breakdown' => 'boolean',
        'shipping_enabled' => 'boolean',
        'local_pickup_enabled' => 'boolean',
        'cod_enabled' => 'boolean',
        'card_payment_enabled' => 'boolean',
        'bank_transfer_enabled' => 'boolean',
        'wallet_payment_enabled' => 'boolean',
        'auto_confirm_orders' => 'boolean',
        'auto_invoice_on_ship' => 'boolean',
        'customer_registration_required' => 'boolean',
        'customer_verification_required' => 'boolean',
        'loyalty_program_enabled' => 'boolean',
        'email_order_confirmation' => 'boolean',
        'email_shipping_updates' => 'boolean',
        'sms_order_updates' => 'boolean',
        'push_notifications_enabled' => 'boolean',
        'tax_rate' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'loyalty_points_per_currency' => 'decimal:2',
        'loyalty_point_value' => 'decimal:2',
        'low_stock_threshold' => 'integer',
        'order_start_number' => 'integer',
        'order_cancellation_window_hours' => 'integer',
        'payment_gateways' => 'array',
        'shipping_zones' => 'array',
        'order_statuses' => 'array',
    ];

    /**
     * Cache key for the singleton settings instance.
     *
     * @var string
     */
    private const string CACHE_KEY = 'tenant_settings_instance';

    /**
     * Cache TTL in seconds (1 hour).
     *
     * @var int
     */
    private const int CACHE_TTL = 3600;

    /**
     * Get the singleton settings record (cached by ID).
     *
     * Only the row ID is cached — not the model — because
     * config/cache.php sets serializable_classes to false.
     *
     * Creates default settings if no record exists.
     */
    public static function instance(): self
    {
        $id = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): int {
            return (int) self::firstOrCreate([], [
                'store_name' => 'My Store',
                'currency' => 'NGN',
            ])->id;
        });

        $instance = self::find($id);

        if ($instance instanceof self) {
            return $instance;
        }

        self::clearCache();

        return self::firstOrCreate([], [
            'store_name' => 'My Store',
            'currency' => 'NGN',
        ]);
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key The setting key (dot notation supported)
     * @param mixed $default Default value if not found
     * @return mixed The setting value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $instance = self::instance();

        return data_get($instance, $key, $default);
    }

    /**
     * Update settings and clear the cache.
     *
     * @param array<string, mixed> $data Key-value pairs to update
     * @return self The updated settings instance
     */
    public static function updateSettings(array $data): self
    {
        $instance = self::instance();
        $instance->update($data);

        self::clearCache();

        return $instance->fresh();
    }

    /**
     * Get the effective store name.
     *
     * @return string The store name
     */
    public static function storeName(): string
    {
        return self::get('store_name', 'My Store');
    }

    /**
     * Get effective branding colors.
     *
     * @return array<string, string|null> Color hex codes
     */
    public static function brandColors(): array
    {
        return [
            'primary' => self::get('primary_color', '#1e2b2e'),
            'accent' => self::get('accent_color', '#73bc1c'),
            'secondary' => self::get('secondary_color'),
        ];
    }

    /**
     * Get currency configuration.
     *
     * @return array<string, mixed> Currency settings
     */
    public static function currencyConfig(): array
    {
        return [
            'code' => self::get('currency', 'NGN'),
            'symbol' => self::get('currency_symbol', '₦'),
            'position' => self::get('currency_position', 'before'),
        ];
    }

    /**
     * Format amount with currency symbol.
     *
     * @param float $amount The amount to format
     * @return string Formatted currency string
     */
    public static function formatMoney(float $amount): string
    {
        $config = self::currencyConfig();
        $formatted = number_format($amount, 2);

        return $config['position'] === 'before'
            ? $config['symbol'] . $formatted
            : $formatted . ' ' . $config['symbol'];
    }

    /**
     * Check if the store is operational.
     *
     * @return bool True if store is open for business
     */
    public static function isStoreOpen(): bool
    {
        return self::get('store_active', true)
            && self::get('checkout_enabled', true);
    }

    /**
     * Get active payment methods.
     *
     * @return array<string> List of active payment method keys
     */
    public static function activePaymentMethods(): array
    {
        $methods = [];

        if (self::get('cod_enabled', true)) {
            $methods[] = 'cash_on_delivery';
        }
        if (self::get('card_payment_enabled', true)) {
            $methods[] = 'card';
        }
        if (self::get('bank_transfer_enabled', false)) {
            $methods[] = 'bank_transfer';
        }
        if (self::get('wallet_payment_enabled', false)) {
            $methods[] = 'wallet';
        }

        return $methods;
    }

    /**
     * Get order number format configuration.
     *
     * @return array<string, mixed> Order formatting settings
     */
    public static function orderFormat(): array
    {
        return [
            'prefix' => self::get('order_prefix', 'ORD-'),
            'start_number' => self::get('order_start_number', 1000),
        ];
    }

    /**
     * Get mail from configuration.
     *
     * @return array<string, string> From address and name
     */
    public static function mailFrom(): array
    {
        return [
            'address' => self::get('notification_email_from', config('mail.from.address')),
            'name' => self::get('notification_email_from_name', self::storeName()),
        ];
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
