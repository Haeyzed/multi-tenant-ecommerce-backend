<?php

declare(strict_types=1);

namespace App\Contracts\Central;

use App\DTOs\Central\Auth\ChangePasswordDTO;
use App\DTOs\Central\Auth\ForgotPasswordDTO;
use App\DTOs\Central\Auth\LoginDTO;
use App\DTOs\Central\Auth\ResetPasswordDTO;
use App\DTOs\Central\Auth\VerifyOtpDTO;
use App\Models\Central\User;

/**
 * Interface AuthServiceInterface
 *
 * Contract for authentication service operations.
 *
 * @package App\Contracts\Central
 */
interface AuthServiceInterface
{
    /**
     * Authenticate a user and create a token.
     *
     * @param LoginDTO $dto
     * @return array<string, mixed>
     */
    public function login(LoginDTO $dto): array;

    /**
     * Register new user.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function register(array $data): array;

    /**
     * Send password reset OTP.
     *
     * @param ForgotPasswordDTO $dto
     * @return array<string, string>
     */
    public function forgotPassword(ForgotPasswordDTO $dto): array;

    /**
     * Verify OTP code.
     *
     * @param VerifyOtpDTO $dto
     * @return array<string, mixed>
     */
    public function verifyOtp(VerifyOtpDTO $dto): array;

    /**
     * Resend email verification OTP.
     *
     * @return array<string, string>
     */
    public function resendVerificationOtp(string $email): array;

    /**
     * Reset password with verified OTP.
     *
     * @param ResetPasswordDTO $dto
     * @return array<string, string>
     */
    public function resetPassword(ResetPasswordDTO $dto): array;

    /**
     * Change the password for an authenticated user.
     *
     * @param User $user
     * @param ChangePasswordDTO $dto
     * @return array<string, string>
     */
    public function changePassword(User $user, ChangePasswordDTO $dto): array;

    /**
     * Get an authenticated user profile.
     *
     * @param User $user
     * @return User
     */
    public function me(User $user): User;

    /**
     * Logout user.
     *
     * @param User $user
     * @return array<string, string>
     */
    public function logout(User $user): array;
}
