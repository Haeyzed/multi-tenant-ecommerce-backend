<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Contracts\Central\SettingServiceInterface;
use App\DTOs\Central\SettingDTO;
use App\Models\Central\Setting;
use App\Repositories\Central\SettingRepository;
use Illuminate\Validation\ValidationException;

readonly class SettingService implements SettingServiceInterface
{
    public function __construct(
        private SettingRepository $repository,
    ) {}

    public function getSettings(): Setting
    {
        return $this->repository->instance();
    }

    public function updateSettings(SettingDTO $dto): Setting
    {
        $attributes = $dto->toArray();

        if ($attributes === []) {
            throw ValidationException::withMessages([
                'settings' => ['No settings were provided to update.'],
            ]);
        }

        return $this->repository->update($attributes);
    }

    public function toggleMaintenanceMode(): Setting
    {
        $setting = $this->repository->instance();

        return $this->repository->update([
            'maintenance_mode' => ! $setting->maintenance_mode,
        ]);
    }
}
