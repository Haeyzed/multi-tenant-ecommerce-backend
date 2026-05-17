<?php

declare(strict_types=1);

namespace App\Contracts\Tenant;

use App\DTOs\Tenant\SettingDTO;

interface SettingServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getAllSettings(): array;

    /**
     * @return array<string, mixed>
     */
    public function getBranding(): array;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSettings(SettingDTO $dto): array;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateBranding(array $data): array;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updatePayments(array $data): array;

    /**
     * @return array<string, mixed>
     */
    public function toggleStoreStatus(): array;
}
