<?php

declare(strict_types=1);

namespace App\Repositories\Central;

use App\Models\Central\Setting;

class SettingRepository
{
    public function instance(): Setting
    {
        return Setting::query()->firstOrCreate([], [
            'site_name' => config('app.name', 'Multi-Tenant E-Commerce'),
            'support_email' => config('mail.from.address', 'support@example.com'),
            'currency' => 'USD',
            'maintenance_mode' => false,
            'trial_days' => 14,
            'default_plan_id' => null,
            'email_notifications' => true,
            'sms_notifications' => false,
        ]);
    }

    public function update(array $data): Setting
    {
        $setting = $this->instance();
        $setting->update($data);

        return $setting->fresh(['defaultPlan']);
    }
}
