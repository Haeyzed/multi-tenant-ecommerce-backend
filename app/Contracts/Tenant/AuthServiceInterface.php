<?php

declare(strict_types=1);

namespace App\Contracts\Tenant;

use App\DTOs\Tenant\Auth\ChangePasswordDTO;
use App\DTOs\Tenant\Auth\ForgotPasswordDTO;
use App\DTOs\Tenant\Auth\LoginDTO;
use App\DTOs\Tenant\Auth\ResetPasswordDTO;
use App\DTOs\Tenant\Auth\VerifyOtpDTO;
use App\Models\Tenant\User;

interface AuthServiceInterface
{
    public function login(LoginDTO $dto): array;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function register(array $data): array;

    public function forgotPassword(ForgotPasswordDTO $dto): array;

    public function verifyOtp(VerifyOtpDTO $dto): array;

    public function resendVerificationOtp(string $email): array;

    public function resetPassword(ResetPasswordDTO $dto): array;

    public function changePassword(User $user, ChangePasswordDTO $dto): array;

    public function me(User $user): array;

    public function logout(User $user): array;
}
