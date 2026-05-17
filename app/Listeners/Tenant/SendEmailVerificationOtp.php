<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\Tenant\VerificationOtpIssued;
use App\Listeners\Tenant\Concerns\BuildsStoreMailTemplateData;
use App\Notifications\Tenant\TemplatedEmailNotification;

class SendEmailVerificationOtp
{
    use BuildsStoreMailTemplateData;

    public function handle(VerificationOtpIssued $event): void
    {
        $event->user->notify(new TemplatedEmailNotification(
            'email_verification_otp',
            $this->otpMailData($event->user->name, $event->otp, $event->expiresIn)
        ));
    }
}
