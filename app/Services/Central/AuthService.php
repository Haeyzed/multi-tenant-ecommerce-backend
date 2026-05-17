<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Contracts\Central\AuthServiceInterface;
use App\DTOs\Central\Auth\ChangePasswordDTO;
use App\DTOs\Central\Auth\ForgotPasswordDTO;
use App\DTOs\Central\Auth\LoginDTO;
use App\DTOs\Central\Auth\ResetPasswordDTO;
use App\DTOs\Central\Auth\VerifyOtpDTO;
use App\Events\Central\PasswordResetOtpIssued;
use App\Events\Central\VerificationOtpIssued;
use App\Models\Central\User;
use App\Repositories\Central\AuthRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

/**
 * Class AuthService
 *
 * Handles all authentication business logic including login,
 * OTP verification, password reset, and session management.
 *
 * @package App\Services\Central
 */
readonly class AuthService implements AuthServiceInterface
{
    /**
     * AuthService constructor.
     *
     * @param AuthRepository $repository Authentication data repository
     */
    public function __construct(
        private AuthRepository $repository
    ) {}

    /**
     * Authenticate a user and create a token.
     *
     * @param LoginDTO $dto Login credentials
     * @return array<string, mixed> Authentication response with token
     * @throws ValidationException When credentials are invalid
     */
    public function login(LoginDTO $dto): array
    {
        $user = $this->repository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Account is suspended.'],
            ]);
        }

        if (!$user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Email not verified. Please verify your email first.'],
            ]);
        }

        // Revoke existing tokens if not remember
        if (!$dto->remember) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load(['roles', 'permissions']),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Send OTP for password reset.
     *
     * @param ForgotPasswordDTO $dto Password reset request
     * @return array<string, string> Success message
     * @throws Exception When rate limited
     */
    public function forgotPassword(ForgotPasswordDTO $dto): array
    {
        $user = $this->repository->findByEmail($dto->email);

        if (!$user) {
            // Return success even if user not found (security)
            return ['message' => 'If the email exists, an OTP has been sent.'];
        }

        if ($this->repository->isRateLimited($dto->email, 'password_reset')) {
            throw new Exception('Please wait before requesting another OTP.');
        }

        $otp = $this->repository->generateOtp($dto->email, 'password_reset');

        event(new PasswordResetOtpIssued($user, $otp));

        return ['message' => 'Password reset OTP has been sent to your email.'];
    }

    /**
     * Verify OTP code.
     *
     * @param VerifyOtpDTO $dto OTP verification data
     * @return array<string, mixed> Verification result with token if applicable
     * @throws ValidationException When OTP is invalid
     */
    public function verifyOtp(VerifyOtpDTO $dto): array
    {
        $isValid = $this->repository->verifyOtp($dto->email, $dto->otp, $dto->type);

        if (!$isValid) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.'],
            ]);
        }

        $user = $this->repository->findByEmail($dto->email);

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        // Handle email verification
        if ($dto->type === 'email_verification') {
            $this->repository->markEmailVerified($user);

            return [
                'message' => 'Email verified successfully.',
                'verified' => true,
            ];
        }

        // Handle password reset verification - return temp token
        if ($dto->type === 'password_reset') {
            $token = $this->repository->createPasswordResetToken($dto->email);

            return [
                'message' => 'OTP verified. You may now reset your password.',
                'reset_token' => $token,
            ];
        }

        return ['message' => 'OTP verified successfully.'];
    }

    /**
     * Reset password with verified OTP.
     *
     * @param ResetPasswordDTO $dto Password reset data
     * @return array<string, string> Success message
     * @throws ValidationException When validation fails
     */
    public function resetPassword(ResetPasswordDTO $dto): array
    {
        if ($dto->password !== $dto->passwordConfirmation) {
            throw ValidationException::withMessages([
                'password' => ['Password confirmation does not match.'],
            ]);
        }

        $user = $this->repository->findByEmail($dto->email);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        $verified = false;

        if ($dto->resetToken !== null) {
            $verified = $this->repository->validatePasswordResetToken($dto->email, $dto->resetToken);
        }

        if (! $verified && $dto->otp !== null) {
            $verified = $this->repository->verifyOtp($dto->email, $dto->otp, 'password_reset');
        }

        if (! $verified) {
            throw ValidationException::withMessages([
                'reset_token' => ['Invalid or expired reset token or OTP.'],
            ]);
        }

        $this->repository->forgetPasswordResetToken($dto->email);
        $this->repository->updatePassword($user, $dto->password);

        $user->tokens()->delete();

        return ['message' => 'Password reset successfully. Please login with your new password.'];
    }

    /**
     * Change the password for an authenticated user.
     *
     * @param User $user The authenticated user
     * @param ChangePasswordDTO $dto Password change data
     * @return array<string, string> Success message
     * @throws ValidationException When current password is wrong
     */
    public function changePassword(User $user, ChangePasswordDTO $dto): array
    {
        if (!Hash::check($dto->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        if ($dto->newPassword !== $dto->newPasswordConfirmation) {
            throw ValidationException::withMessages([
                'new_password' => ['Password confirmation does not match.'],
            ]);
        }

        $this->repository->updatePassword($user, $dto->newPassword);

        // Revoke all tokens except current
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return ['message' => 'Password changed successfully.'];
    }

    /**
     * Get an authenticated user profile.
     *
     * @param User $user The authenticated user
     * @return array<string, mixed> User profile with roles and permissions
     */
    public function me(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getPermissionNames(),
            'direct_permissions' => $user->getDirectPermissions()->pluck('name'),
            'created_at' => $user->created_at->toDateTimeString(),
        ];
    }

    /**
     * Log out user and revoke the token.
     *
     * @param User $user The authenticated user
     * @return array<string, string> Success message
     */
    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();

        return ['message' => 'Logged out successfully.'];
    }

    /**
     * Resend email verification OTP for an unverified account.
     *
     * @return array<string, string>
     * @throws Exception
     */
    public function resendVerificationOtp(string $email): array
    {
        $user = $this->repository->findByEmail($email);

        if (! $user) {
            return ['message' => 'If the email exists, a verification OTP has been sent.'];
        }

        if ($user->email_verified_at !== null) {
            return ['message' => 'Email is already verified.'];
        }

        if ($this->repository->isRateLimited($email, 'email_verification')) {
            throw new Exception('Please wait before requesting another OTP.');
        }

        $otp = $this->repository->generateOtp($email, 'email_verification');

        event(new VerificationOtpIssued($user, $otp));

        return ['message' => 'Verification OTP has been sent to your email.'];
    }

    /**
     * Register a new user and send verification OTP.
     *
     * @param array<string, mixed> $data Registration data
     * @return array<string, mixed> Created user and OTP status
     * @throws RandomException
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_active' => true,
        ]);

        $user->assignRole('viewer');

        $otp = $this->repository->generateOtp($user->email, 'email_verification');

        event(new VerificationOtpIssued($user, $otp));

        return [
            'user' => $user,
            'message' => 'Registration successful. Please verify your email with the OTP sent.',
        ];
    }
}
