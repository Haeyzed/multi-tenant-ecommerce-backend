<?php

declare(strict_types=1);

namespace App\Repositories\Central;

use App\Models\Central\Setting;

class SettingRepository
{
    public function instance(): Setting
    {
        return Setting::instance();
    }

    public function update(array $data): Setting
    {
        return Setting::updateSettings($data);
    }
}
