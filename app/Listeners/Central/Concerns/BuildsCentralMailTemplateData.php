<?php

declare(strict_types=1);

namespace App\Listeners\Central\Concerns;

use App\Models\Central\Setting;

trait BuildsCentralMailTemplateData
{
    protected function platformName(): string
    {
        return Setting::platformName();
    }

    protected function centralLoginUrl(): string
    {
        return rtrim((string) config('app.url'), '/').'/login';
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
            'platform_name' => $this->platformName(),
        ];
    }
}
