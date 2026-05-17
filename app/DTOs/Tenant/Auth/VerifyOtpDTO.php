<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Auth;

/**
 * Class VerifyOtpDTO
 *
 * Data transfer object for OTP verification.
 *
 * @package App\DTOs\Tenant\Auth
 */
readonly class VerifyOtpDTO
{
    /**
     * Create a new VerifyOtpDTO instance.
     *
     * @param string $email The user's email address
     * @param string $otp The one-time password code
     * @param string $type The verification type (email_verification, password_reset)
     */
    public function __construct(
        public string $email,
        public string $otp,
        public string $type
    ) {}

    /**
     * Create from validated request data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            otp: $data['otp'],
            type: $data['type'] ?? 'email_verification'
        );
    }
}
