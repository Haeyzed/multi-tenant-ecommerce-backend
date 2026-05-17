<?php

declare(strict_types=1);

namespace App\Contracts\Central;

use App\DTOs\Central\SettingDTO;
use App\Models\Central\Setting;

interface SettingServiceInterface
{
    public function getSettings(): Setting;

    public function updateSettings(SettingDTO $dto): Setting;

    public function toggleMaintenanceMode(): Setting;
}
