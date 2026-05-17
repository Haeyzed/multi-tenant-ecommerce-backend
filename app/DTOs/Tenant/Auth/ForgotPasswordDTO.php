<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Auth;

/**
 * Class ForgotPasswordDTO
 *
 * Data transfer object for password reset request.
 *
 * @package App\DTOs\Tenant\Auth
 */
readonly class ForgotPasswordDTO
{
    /**
     * Create a new ForgotPasswordDTO instance.
     *
     * @param string $email The user's registered email address
     */
    public function __construct(
        public string $email
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
            email: $data['email']
        );
    }
}
