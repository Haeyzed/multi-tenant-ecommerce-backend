<?php

declare(strict_types=1);

namespace App\DTOs\Central\Auth;

/**
 * Class LoginDTO
 *
 * Data transfer object for user login credentials.
 *
 * @package App\DTOs\Central\Auth
 */
readonly class LoginDTO
{
    /**
     * Create a new LoginDTO instance.
     *
     * @param string $email The user's email address
     * @param string $password The user's plain text password
     * @param bool $remember Whether to remember the login session
     */
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false
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
            remember: $data['remember'] ?? false
        );
    }

    /**
     * Convert to array for an authentication attempt.
     *
     * @return array<string, string>
     */
    public function toCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
