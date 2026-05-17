<?php

declare(strict_types=1);

namespace App\Listeners\Central;

use App\Events\Central\PasswordResetOtpIssued;
use App\Listeners\Central\Concerns\BuildsCentralMailTemplateData;
use App\Notifications\Central\TemplatedEmailNotification;

class SendPasswordResetOtp
{
    use BuildsCentralMailTemplateData;

    public function handle(PasswordResetOtpIssued $event): void
    {
        $event->user->notify(new TemplatedEmailNotification(
            'password_reset_otp',
            $this->otpMailData($event->user->name, $event->otp, $event->expiresIn)
        ));
    }
}
