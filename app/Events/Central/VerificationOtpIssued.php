<?php

declare(strict_types=1);

namespace App\Events\Central;

use App\Models\Central\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an email verification OTP is generated (register or resend).
 */
class VerificationOtpIssued
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $otp,
        public readonly string $expiresIn = '15 minutes',
    ) {}
}
