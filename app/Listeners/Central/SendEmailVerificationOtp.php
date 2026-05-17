<?php

declare(strict_types=1);

namespace App\Listeners\Central;

use App\Events\Central\VerificationOtpIssued;
use App\Listeners\Central\Concerns\BuildsCentralMailTemplateData;
use App\Notifications\Central\TemplatedEmailNotification;

class SendEmailVerificationOtp
{
    use BuildsCentralMailTemplateData;

    public function handle(VerificationOtpIssued $event): void
    {
        $event->user->notify(new TemplatedEmailNotification(
            'email_verification_otp',
            $this->otpMailData($event->user->name, $event->otp, $event->expiresIn)
        ));
    }
}
