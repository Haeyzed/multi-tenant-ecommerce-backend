<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // === BRANDING ===
            $table->string('store_name')->default('My Store');
            $table->string('store_tagline')->nullable();
            $table->string('store_logo_url')->nullable();
            $table->string('store_logo_dark_url')->nullable();
            $table->string('store_favicon_url')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('accent_color')->nullable();
            $table->string('secondary_color')->nullable();

            // === CONTACT ===
            $table->string('store_email')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('store_whatsapp')->nullable();
            $table->text('store_address')->nullable();
            $table->string('store_website')->nullable();

            // === BUSINESS ===
            $table->string('business_type')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('industry')->nullable();

            // === E-COMMERCE CORE ===
            $table->boolean('store_active')->default(true);
            $table->boolean('catalog_visible')->default(true);
            $table->boolean('checkout_enabled')->default(true);
            $table->boolean('guest_checkout_allowed')->default(true);
            $table->boolean('wishlist_enabled')->default(true);
            $table->boolean('reviews_enabled')->default(true);
            $table->boolean('compare_products_enabled')->default(false);
            $table->boolean('inventory_tracking_enabled')->default(true);
            $table->boolean('low_stock_alerts_enabled')->default(true);
            $table->integer('low_stock_threshold')->default(10);

            // === PRICING & TAX ===
            $table->string('currency')->default('NGN');
            $table->string('currency_symbol')->default('₦');
            $table->string('currency_position')->default('before');
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->boolean('prices_include_tax')->default(false);
            $table->boolean('show_tax_breakdown')->default(true);
            $table->decimal('minimum_order_amount', 12, 2)->default(0.00);
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();

            // === SHIPPING ===
            $table->boolean('shipping_enabled')->default(true);
            $table->boolean('local_pickup_enabled')->default(false);
            $table->string('default_weight_unit')->default('kg');
            $table->string('default_dimension_unit')->default('cm');
            $table->json('shipping_zones')->nullable();

            // === PAYMENTS ===
            $table->boolean('cod_enabled')->default(true);
            $table->boolean('card_payment_enabled')->default(true);
            $table->boolean('bank_transfer_enabled')->default(false);
            $table->boolean('wallet_payment_enabled')->default(false);
            $table->json('payment_gateways')->nullable();
            $table->string('paystack_public_key')->nullable();
            $table->string('paystack_secret_key')->nullable();
            $table->string('flutterwave_public_key')->nullable();
            $table->string('flutterwave_secret_key')->nullable();

            // === ORDERS ===
            $table->string('order_prefix')->default('ORD-');
            $table->integer('order_start_number')->default(1000);
            $table->boolean('auto_confirm_orders')->default(false);
            $table->boolean('auto_invoice_on_ship')->default(true);
            $table->json('order_statuses')->nullable();
            $table->integer('order_cancellation_window_hours')->default(24);

            // === CUSTOMERS ===
            $table->boolean('customer_registration_required')->default(true);
            $table->boolean('customer_verification_required')->default(true);
            $table->boolean('loyalty_program_enabled')->default(false);
            $table->decimal('loyalty_points_per_currency', 8, 2)->default(1.00);
            $table->decimal('loyalty_point_value', 8, 2)->default(1.00);

            // === NOTIFICATIONS ===
            $table->boolean('email_order_confirmation')->default(true);
            $table->boolean('email_shipping_updates')->default(true);
            $table->boolean('sms_order_updates')->default(false);
            $table->boolean('push_notifications_enabled')->default(false);
            $table->string('notification_email_from')->nullable();
            $table->string('notification_email_from_name')->nullable();

            // === SEO ===
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->string('facebook_pixel_id')->nullable();

            // === SOCIAL ===
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();

            // === LEGAL ===
            $table->text('terms_and_conditions')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('refund_policy')->nullable();
            $table->text('shipping_policy')->nullable();

            // === LOCALIZATION ===
            $table->string('language')->default('en');
            $table->string('timezone')->default('Africa/Lagos');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
