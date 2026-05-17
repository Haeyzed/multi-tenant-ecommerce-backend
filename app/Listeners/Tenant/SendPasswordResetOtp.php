<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\Tenant\PasswordResetOtpIssued;
use App\Listeners\Tenant\Concerns\BuildsStoreMailTemplateData;
use App\Notifications\Tenant\TemplatedEmailNotification;

class SendPasswordResetOtp
{
    use BuildsStoreMailTemplateData;

    public function handle(PasswordResetOtpIssued $event): void
    {
        $event->user->notify(new TemplatedEmailNotification(
            'password_reset_otp',
            $this->otpMailData($event->user->name, $event->otp, $event->expiresIn)
        ));
    }
}
