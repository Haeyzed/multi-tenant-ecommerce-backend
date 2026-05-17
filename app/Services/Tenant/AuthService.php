<?php

declare(strict_types=1);

namespace App\Services\Tenant;

use App\Contracts\Tenant\AuthServiceInterface;
use App\DTOs\Tenant\Auth\ChangePasswordDTO;
use App\DTOs\Tenant\Auth\ForgotPasswordDTO;
use App\DTOs\Tenant\Auth\LoginDTO;
use App\DTOs\Tenant\Auth\ResetPasswordDTO;
use App\DTOs\Tenant\Auth\VerifyOtpDTO;
use App\Events\Tenant\PasswordResetOtpIssued;
use App\Events\Tenant\VerificationOtpIssued;
use App\Models\Tenant\User;
use App\Repositories\Tenant\AuthRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        private AuthRepository $repository,
    ) {}

    public function login(LoginDTO $dto): array
    {
        $user = $this->repository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if (! $user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Email not verified. Please verify your email first.'],
            ]);
        }

        if (! $dto->remember) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function forgotPassword(ForgotPasswordDTO $dto): array
    {
        $user = $this->repository->findByEmail($dto->email);

        if (! $user) {
            return ['message' => 'If the email exists, an OTP has been sent.'];
        }

        if ($this->repository->isRateLimited($dto->email, 'password_reset')) {
            throw new Exception('Please wait before requesting another OTP.');
        }

        $otp = $this->repository->generateOtp($dto->email, 'password_reset');

        event(new PasswordResetOtpIssued($user, $otp));

        return ['message' => 'Password reset OTP has been sent to your email.'];
    }

    public function verifyOtp(VerifyOtpDTO $dto): array
    {
        if (! $this->repository->verifyOtp($dto->email, $dto->otp, $dto->type)) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.'],
            ]);
        }

        $user = $this->repository->findByEmail($dto->email);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        if ($dto->type === 'email_verification') {
            $this->repository->markEmailVerified($user);

            return [
                'message' => 'Email verified successfully.',
                'verified' => true,
            ];
        }

        if ($dto->type === 'password_reset') {
            return [
                'message' => 'OTP verified. You may now reset your password.',
                'reset_token' => $this->repository->createPasswordResetToken($dto->email),
            ];
        }

        return ['message' => 'OTP verified successfully.'];
    }

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

    public function changePassword(User $user, ChangePasswordDTO $dto): array
    {
        if (! Hash::check($dto->currentPassword, $user->password)) {
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

        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return ['message' => 'Password changed successfully.'];
    }

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
            'created_at' => $user->created_at->toDateTimeString(),
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();

        return ['message' => 'Logged out successfully.'];
    }

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
     * @param array<string, mixed> $data
     * @throws RandomException
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_active' => true,
        ]);

        if ($user->roles()->count() === 0) {
            $user->assignRole('viewer');
        }

        $otp = $this->repository->generateOtp($user->email, 'email_verification');

        event(new VerificationOtpIssued($user, $otp));

        return [
            'user' => $user,
            'message' => 'Registration successful. Please verify your email with the OTP sent.',
        ];
    }
}
