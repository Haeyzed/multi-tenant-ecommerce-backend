<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Auth;

/**
 * Class ChangePasswordDTO
 *
 * Data transfer object for authenticated password change.
 *
 * @package App\DTOs\Tenant\Auth
 */
readonly class ChangePasswordDTO
{
    /**
     * Create a new ChangePasswordDTO instance.
     *
     * @param string $currentPassword The user's current password
     * @param string $newPassword The new password to set
     * @param string $newPasswordConfirmation Confirmation of new password
     */
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
        public string $newPasswordConfirmation
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
            currentPassword: $data['current_password'],
            newPassword: $data['new_password'],
            newPasswordConfirmation: $data['new_password_confirmation']
        );
    }
}
