<?php

namespace App\Repositories\Central;

use App\Models\Central\Setting;

class SettingRepository
{
    /**
     * Get the settings.
     *
     * @return Setting|null
     */
    public function get(): ?Setting
    {
        return Setting::first();
    }

    /**
     * Update the settings.
     *
     * @param array $data
     * @return Setting
     */
    public function update(array $data): Setting
    {
        $setting = Setting::first() ?? new Setting();

        $setting->fill($data);
        $setting->save();

        return $setting;
    }
}
