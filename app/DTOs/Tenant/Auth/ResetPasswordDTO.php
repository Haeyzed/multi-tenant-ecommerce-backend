<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Auth;

/**
 * Class ResetPasswordDTO
 *
 * Data transfer object for password reset with OTP.
 *
 * @package App\DTOs\Tenant\Auth
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
        public string $password,
        public string $passwordConfirmation,
        public ?string $otp = null,
        public ?string $resetToken = null,
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
            password: $data['password'],
            passwordConfirmation: $data['password_confirmation'],
            otp: $data['otp'] ?? null,
            resetToken: $data['reset_token'] ?? null,
        );
    }
}
