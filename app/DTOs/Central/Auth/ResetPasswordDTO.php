<?php

declare(strict_types=1);

namespace App\DTOs\Central\Auth;

/**
 * Class ResetPasswordDTO
 *
 * Data transfer object for password reset with OTP.
 *
 * @package App\DTOs\Central\Auth
 */
readonly class ResetPasswordDTO
{
    /**
     * Create a new ResetPasswordDTO instance.
     *
     * @param string $email The user's email address
     * @param string $otp The verified OTP code
     * @param string $password The new password
     * @param string $passwordConfirmation Password confirmation (must match)
     */
    public function __construct(
        public string $email,
        public string $otp,
        public string $password,
        public string $passwordConfirmation
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
            password: $data['password'],
            passwordConfirmation: $data['password_confirmation']
        );
    }
}
