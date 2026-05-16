<?php

declare(strict_types=1);

namespace App\Services\Tenant;

use App\Contracts\Tenant\SettingServiceInterface;
use App\DTOs\Tenant\SettingDTO;
use App\Repositories\Tenant\SettingRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class SettingService
 *
 * Business logic for tenant settings management.
 *
 * @package App\Services\Tenant
 */
readonly class SettingService implements SettingServiceInterface
{
    /**
     * SettingService constructor.
     *
     * @param SettingRepository $repository
     */
    public function __construct(
        private SettingRepository $repository
    ) {}

    /**
     * Get all tenant settings.
     *
     * @return array<string, mixed> Complete settings data
     */
    public function getAllSettings(): array
    {
        $settings = $this->repository->getSettings();

        return [
            'branding' => $this->repository->getBranding(),
            'contact' => [
                'email' => $settings->store_email,
                'phone' => $settings->store_phone,
                'whatsapp' => $settings->store_whatsapp,
                'address' => $settings->store_address,
                'website' => $settings->store_website,
            ],
            'commerce' => $this->repository->getCommerceConfig(),
            'payments' => $this->repository->getPaymentConfig(),
            'shipping' => [
                'enabled' => $settings->shipping_enabled,
                'local_pickup' => $settings->local_pickup_enabled,
                'weight_unit' => $settings->default_weight_unit,
                'dimension_unit' => $settings->default_dimension_unit,
                'zones' => $settings->shipping_zones,
            ],
            'orders' => [
                'prefix' => $settings->order_prefix,
                'start_number' => $settings->order_start_number,
                'auto_confirm' => $settings->auto_confirm_orders,
                'auto_invoice' => $settings->auto_invoice_on_ship,
                'cancellation_window' => $settings->order_cancellation_window_hours,
            ],
            'customers' => [
                'registration_required' => $settings->customer_registration_required,
                'verification_required' => $settings->customer_verification_required,
                'loyalty_enabled' => $settings->loyalty_program_enabled,
            ],
            'notifications' => [
                'email_order' => $settings->email_order_confirmation,
                'email_shipping' => $settings->email_shipping_updates,
                'sms_order' => $settings->sms_order_updates,
                'push_enabled' => $settings->push_notifications_enabled,
            ],
            'localization' => [
                'language' => $settings->language,
                'timezone' => $settings->timezone,
                'date_format' => $settings->date_format,
                'time_format' => $settings->time_format,
            ],
            'seo' => [
                'meta_title' => $settings->meta_title,
                'meta_description' => $settings->meta_description,
                'meta_keywords' => $settings->meta_keywords,
            ],
            'social' => [
                'facebook' => $settings->facebook_url,
                'instagram' => $settings->instagram_url,
                'twitter' => $settings->twitter_url,
                'linkedin' => $settings->linkedin_url,
                'youtube' => $settings->youtube_url,
                'tiktok' => $settings->tiktok_url,
            ],
            'legal' => [
                'terms' => $settings->terms_and_conditions,
                'privacy' => $settings->privacy_policy,
                'refund' => $settings->refund_policy,
                'shipping' => $settings->shipping_policy,
            ],
        ];
    }

    /**
     * Get branding settings only.
     *
     * @return array<string, mixed>
     */
    public function getBranding(): array
    {
        return $this->repository->getBranding();
    }

    /**
     * Update settings.
     *
     * @param SettingDTO $dto
     * @return array<string, mixed> Updated settings
     * @throws ValidationException
     */
    public function updateSettings(SettingDTO $dto): array
    {
        $data = $dto->toArray();

        $this->validateUpdate($data);

        $settings = $this->repository->update($data);

        return [
            'message' => 'Settings updated successfully',
            'settings' => $settings->toArray(),
        ];
    }

    /**
     * Update branding only.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateBranding(array $data): array
    {
        $allowed = [
            'store_name',
            'store_tagline',
            'store_logo_url',
            'store_logo_dark_url',
            'store_favicon_url',
            'primary_color',
            'accent_color',
            'secondary_color',
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        $settings = $this->repository->update($filtered);

        return [
            'message' => 'Branding updated successfully',
            'branding' => $this->repository->getBranding(),
        ];
    }

    /**
     * Update payment settings.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updatePayments(array $data): array
    {
        $allowed = [
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

        $filtered = array_intersect_key($data, array_flip($allowed));

        $settings = $this->repository->update($filtered);

        return [
            'message' => 'Payment settings updated successfully',
            'payments' => $this->repository->getPaymentConfig(),
        ];
    }

    /**
     * Toggle store status (active/inactive).
     *
     * @return array<string, mixed>
     */
    public function toggleStoreStatus(): array
    {
        $current = $this->repository->getValue('store_active', true);
        $settings = $this->repository->update(['store_active' => !$current]);

        return [
            'message' => 'Store status updated',
            'store_active' => $settings->store_active,
        ];
    }

    /**
     * Validate settings update data.
     *
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    private function validateUpdate(array $data): void
    {
        $validator = Validator::make($data, [
            'store_name' => 'sometimes|string|max:255',
            'store_email' => 'sometimes|email|nullable',
            'store_phone' => 'sometimes|string|max:20|nullable',
            'currency' => 'sometimes|string|size:3',
            'tax_rate' => 'sometimes|numeric|min:0|max:100',
            'primary_color' => 'sometimes|string|regex:/^#[a-fA-F0-9]{6}$/|nullable',
            'accent_color' => 'sometimes|string|regex:/^#[a-fA-F0-9]{6}$/|nullable',
            'minimum_order_amount' => 'sometimes|numeric|min:0',
            'low_stock_threshold' => 'sometimes|integer|min:1',
            'order_prefix' => 'sometimes|string|max:10',
            'language' => 'sometimes|string|size:2',
            'timezone' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
