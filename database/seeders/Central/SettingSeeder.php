<?php

namespace Database\Seeders\Central;

use App\Models\Central\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->create([
            'site_name' => 'Multi-Tenant E-Commerce',
            'support_email' => 'support@example.com',
            'currency' => 'USD',
            'maintenance_mode' => false,
            'trial_days' => 14,
            'default_plan_id' => 1,
            'email_notifications' => true,
            'sms_notifications' => false,
        ]);
    }
}
