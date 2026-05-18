<?php

declare(strict_types=1);

namespace App\Services\Central;

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
 * Contains the core business logic for user authentication, registration,
 * and account recovery processes.
 *
 * @package App\Services\Central
 */
readonly class AuthService
{
    /**
     * AuthService constructor.
     *
     * @param AuthRepository $repository
     */
    public function __construct(
        private AuthRepository $repository
    ) {}

    /**
     * Authenticate a user and issue a Sanctum token.
     *
     * @param array<string, mixed> $data
     * @return array{user: User, token: string, token_type: string}
     * @throws ValidationException
     */
    public function login(array $data): array
    {
        $user = $this->repository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
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

        if (!($data['remember'] ?? false)) {
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
     * Initiate the password reset process by issuing an OTP.
     *
     * @param string $email
     * @return array{message: string}
     * @throws Exception
     * @throws RandomException
     */
    public function forgotPassword(string $email): array
    {
        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return ['message' => 'If the email exists, an OTP has been sent.'];
        }

        if ($this->repository->isRateLimited($email, 'password_reset')) {
            throw new Exception('Please wait before requesting another OTP.');
        }

        $otp = $this->repository->generateOtp($email, 'password_reset');
        event(new PasswordResetOtpIssued($user, $otp));

        return ['message' => 'Password reset OTP has been sent to your email.'];
    }

    /**
     * Verify a given OTP for either email verification or password reset.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function verifyOtp(array $data): array
    {
        $isValid = $this->repository->verifyOtp($data['email'], $data['otp'], $data['type']);

        if (!$isValid) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.'],
            ]);
        }

        $user = $this->repository->findByEmail($data['email']);

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        if ($data['type'] === 'email_verification') {
            $this->repository->markEmailVerified($user);

            return [
                'message' => 'Email verified successfully.',
                'verified' => true,
            ];
        }

        if ($data['type'] === 'password_reset') {
            $token = $this->repository->createPasswordResetToken($data['email']);

            return [
                'message' => 'OTP verified. You may now reset your password.',
                'reset_token' => $token,
            ];
        }

        return ['message' => 'OTP verified successfully.'];
    }

    /**
     * Complete the password reset process using a new password.
     *
     * @param array<string, mixed> $data
     * @return array{message: string}
     * @throws ValidationException
     */
    public function resetPassword(array $data): array
    {
        $user = $this->repository->findByEmail($data['email']);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        $verified = false;

        if (isset($data['reset_token'])) {
            $verified = $this->repository->validatePasswordResetToken($data['email'], $data['reset_token']);
        }

        if (! $verified && isset($data['otp'])) {
            $verified = $this->repository->verifyOtp($data['email'], $data['otp'], 'password_reset');
        }

        if (! $verified) {
            throw ValidationException::withMessages([
                'reset_token' => ['Invalid or expired reset token or OTP.'],
            ]);
        }

        $this->repository->forgetPasswordResetToken($data['email']);
        $this->repository->updatePassword($user, $data['password']);

        $user->tokens()->delete();

        return ['message' => 'Password reset successfully. Please login with your new password.'];
    }

    /**
     * Allow an authenticated user to change their existing password.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return array{message: string}
     * @throws ValidationException
     */
    public function changePassword(User $user, array $data): array
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $this->repository->updatePassword($user, $data['new_password']);

        // Revoke all tokens except current
        if ($user->currentAccessToken()) {
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
        } else {
            $user->tokens()->delete();
        }

        return ['message' => 'Password changed successfully.'];
    }

    /**
     * Retrieve the authenticated user's profile with relationships.
     *
     * @param User $user
     * @return User
     */
    public function me(User $user): User
    {
        return $user->load(['roles', 'permissions']);
    }

    /**
     * Revoke the user's current access token.
     *
     * @param User $user
     * @return bool|null
     */
    public function logout(User $user): bool|null
    {
        return $user->currentAccessToken()?->delete();
    }

    /**
     * Re-issue an email verification OTP.
     *
     * @param string $email
     * @return array{message: string}
     * @throws Exception
     * @throws RandomException
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
     * Register a new user and dispatch a verification OTP.
     *
     * @param array<string, mixed> $data
     * @return User
     * @throws RandomException
     */
    public function register(array $data): User
    {
        /** @var User $user */
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

        return $user;
    }
}
