<?php

declare(strict_types=1);

namespace App\Listeners\Tenant\Concerns;

use App\Models\Tenant\Setting;

trait BuildsStoreMailTemplateData
{
    protected function storeName(): string
    {
        return Setting::storeName();
    }

    protected function storeLoginUrl(): string
    {
        return url('/login');
    }

    /**
     * @return array<string, string>
     */
    protected function otpMailData(string $userName, string $otp, string $expiresIn = '15 minutes'): array
    {
        return [
            'user_name' => $userName,
            'otp' => $otp,
            'expires_in' => $expiresIn,
            'store_name' => $this->storeName(),
        ];
    }
}
