<?php

declare(strict_types=1);

namespace App\DTOs\Central;

readonly class SettingDTO
{
    public function __construct(
        public ?string $siteName = null,
        public ?string $siteLogoUrl = null,
        public ?string $primaryColor = null,
        public ?string $accentColor = null,
        public ?string $secondaryColor = null,
        public ?string $supportEmail = null,
        public ?string $currency = null,
        public ?bool $maintenanceMode = null,
        public ?int $trialDays = null,
        public ?int $defaultPlanId = null,
        public ?bool $emailNotifications = null,
        public ?bool $smsNotifications = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            siteName: $data['site_name'] ?? null,
            siteLogoUrl: $data['site_logo_url'] ?? null,
            primaryColor: $data['primary_color'] ?? null,
            accentColor: $data['accent_color'] ?? null,
            secondaryColor: $data['secondary_color'] ?? null,
            supportEmail: $data['support_email'] ?? null,
            currency: $data['currency'] ?? null,
            maintenanceMode: array_key_exists('maintenance_mode', $data)
                ? (bool) $data['maintenance_mode']
                : null,
            trialDays: isset($data['trial_days']) ? (int) $data['trial_days'] : null,
            defaultPlanId: isset($data['default_plan_id']) ? (int) $data['default_plan_id'] : null,
            emailNotifications: array_key_exists('email_notifications', $data)
                ? (bool) $data['email_notifications']
                : null,
            smsNotifications: array_key_exists('sms_notifications', $data)
                ? (bool) $data['sms_notifications']
                : null,
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->siteName !== null) {
            $data['site_name'] = $this->siteName;
        }

        if ($this->siteLogoUrl !== null) {
            $data['site_logo_url'] = $this->siteLogoUrl;
        }

        if ($this->primaryColor !== null) {
            $data['primary_color'] = $this->primaryColor;
        }

        if ($this->accentColor !== null) {
            $data['accent_color'] = $this->accentColor;
        }

        if ($this->secondaryColor !== null) {
            $data['secondary_color'] = $this->secondaryColor;
        }

        if ($this->supportEmail !== null) {
            $data['support_email'] = $this->supportEmail;
        }

        if ($this->currency !== null) {
            $data['currency'] = $this->currency;
        }

        if ($this->maintenanceMode !== null) {
            $data['maintenance_mode'] = $this->maintenanceMode;
        }

        if ($this->trialDays !== null) {
            $data['trial_days'] = $this->trialDays;
        }

        if ($this->defaultPlanId !== null) {
            $data['default_plan_id'] = $this->defaultPlanId;
        }

        if ($this->emailNotifications !== null) {
            $data['email_notifications'] = $this->emailNotifications;
        }

        if ($this->smsNotifications !== null) {
            $data['sms_notifications'] = $this->smsNotifications;
        }

        return $data;
    }
}
