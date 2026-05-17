<?php

namespace Database\Seeders\Central;

use App\Models\Central\Plan;
use App\Models\Central\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->firstOrCreate([], [
            'site_name' => config('app.name', 'Multi-Tenant E-Commerce'),
            'support_email' => config('mail.from.address', 'support@example.com'),
            'currency' => 'USD',
            'maintenance_mode' => false,
            'trial_days' => 14,
            'default_plan_id' => Plan::query()->where('is_active', true)->orderBy('id')->value('id'),
            'email_notifications' => true,
            'sms_notifications' => false,
        ]);
    }
}
