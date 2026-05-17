<?php

declare(strict_types=1);

namespace App\Repositories\Central;

use App\Models\Central\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Random\RandomException;

/**
 * Class AuthRepository
 *
 * Handles authentication-related data operations including
 * OTP generation, verification, and token management.
 *
 * @package App\Repositories\Central
 */
class AuthRepository
{
    /**
     * OTP cache TTL in minutes.
     *
     * @var int
     */
    private const int OTP_TTL = 15;

    /**
     * OTP cache prefix.
     *
     * @var string
     */
    private const string OTP_PREFIX = 'auth_otp';

    /**
     * Find a user by email.
     *
     * @param string $email The user's email address
     * @return User|null The user instance or null
     */
    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    /**
     * Generate and store OTP for the user.
     *
     * @param string $email The user's email
     * @param string $type The OTP type (email_verification, password_reset)
     * @return string The generated 6-digit OTP
     * @throws RandomException
     */
    public function generateOtp(string $email, string $type): string
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $key = $this->otpKey($email, $type);
        Cache::put($key, $otp, now()->addMinutes(self::OTP_TTL));

        return $otp;
    }

    /**
     * Verify OTP code.
     *
     * @param string $email The user's email
     * @param string $otp The OTP to verify
     * @param string $type The OTP type
     * @return bool True if valid, false otherwise
     */
    public function verifyOtp(string $email, string $otp, string $type): bool
    {
        $key = $this->otpKey($email, $type);
        $stored = Cache::get($key);

        if ($stored === null || $stored !== $otp) {
            return false;
        }

        // Clear OTP after successful verification
        Cache::forget($key);

        return true;
    }

    /**
     * Mark email as verified.
     *
     * @param User $user The user to verify
     * @return void
     */
    public function markEmailVerified(User $user): void
    {
        $user->update(['email_verified_at' => now()]);
    }

    /**
     * Update user password.
     *
     * @param User $user The user to update
     * @param string $password The hashed password
     * @return void
     */
    public function updatePassword(User $user, string $plainPassword): void
    {
        $user->update([
            'password' => $plainPassword,
        ]);
    }

    /**
     * Create a password reset token.
     *
     * @param string $email The user's email
     * @return string The reset token
     */
    public function createPasswordResetToken(string $email): string
    {
        $token = Str::random(64);

        Cache::put(
            $this->passwordResetKey($email),
            $token,
            now()->addMinutes(60)
        );

        return $token;
    }

    /**
     * Validate a password reset token issued after OTP verification.
     */
    public function validatePasswordResetToken(string $email, string $token): bool
    {
        $stored = Cache::get($this->passwordResetKey($email));

        return is_string($stored) && hash_equals($stored, $token);
    }

    /**
     * Remove the password reset token after a successful reset.
     */
    public function forgetPasswordResetToken(string $email): void
    {
        Cache::forget($this->passwordResetKey($email));
    }

    private function passwordResetKey(string $email): string
    {
        return 'password_reset:'.md5(strtolower($email));
    }

    /**
     * Check if OTP was recently sent (rate limiting).
     *
     * @param string $email The user's email
     * @param string $type The OTP type
     * @return bool True if rate limited
     */
    public function isRateLimited(string $email, string $type): bool
    {
        $key = "otp_rate_limit:$email:$type";

        if (Cache::has($key)) {
            return true;
        }

        // Set rate limit for 60 seconds
        Cache::put($key, true, now()->addSeconds(60));

        return false;
    }

    /**
     * Build OTP cache key.
     *
     * @param string $email The user's email
     * @param string $type The OTP type
     * @return string The cache key
     */
    private function otpKey(string $email, string $type): string
    {
        return self::OTP_PREFIX . ":$type:" . md5($email);
    }
}
