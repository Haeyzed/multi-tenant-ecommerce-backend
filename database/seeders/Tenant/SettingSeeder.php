<?php

declare(strict_types=1);

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Setting;
use Illuminate\Database\Seeder;

/**
 * Class SettingSeeder
 *
 * Seeds default settings for a new tenant database.
 * Run automatically during tenant provisioning.
 *
 * @package Database\Seeders\Tenant
 */
class SettingSeeder extends Seeder
{
    /**
     * Run the tenant settings database seeds.
     *
     * Creates default e-commerce settings for a newly provisioned tenant.
     * These values can be customized by the tenant admin after onboarding.
     *
     * @return void
     */
    public function run(): void
    {
        Setting::updateSettings([
            // Store basics
            'store_name' => 'My Store',
            'store_tagline' => null,
            'store_active' => true,
            'catalog_visible' => true,

            // Commerce
            'checkout_enabled' => true,
            'guest_checkout_allowed' => true,
            'wishlist_enabled' => true,
            'reviews_enabled' => true,

            // Inventory
            'inventory_tracking_enabled' => true,
            'low_stock_alerts_enabled' => true,
            'low_stock_threshold' => 10,

            // Currency
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'currency_position' => 'before',
            'tax_rate' => 0.00,
            'prices_include_tax' => false,
            'show_tax_breakdown' => true,
            'minimum_order_amount' => 0.00,

            // Shipping
            'shipping_enabled' => true,
            'local_pickup_enabled' => false,
            'default_weight_unit' => 'kg',
            'default_dimension_unit' => 'cm',

            // Payments
            'cod_enabled' => true,
            'card_payment_enabled' => true,
            'bank_transfer_enabled' => false,
            'wallet_payment_enabled' => false,

            // Orders
            'order_prefix' => 'ORD-',
            'order_start_number' => 1000,
            'auto_confirm_orders' => false,
            'auto_invoice_on_ship' => true,
            'order_cancellation_window_hours' => 24,

            // Customers
            'customer_registration_required' => true,
            'customer_verification_required' => true,
            'loyalty_program_enabled' => false,
            'loyalty_points_per_currency' => 1.00,
            'loyalty_point_value' => 1.00,

            // Notifications
            'email_order_confirmation' => true,
            'email_shipping_updates' => true,
            'sms_order_updates' => false,
            'push_notifications_enabled' => false,

            // Localization
            'language' => 'en',
            'timezone' => 'Africa/Lagos',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
        ]);
    }
}
