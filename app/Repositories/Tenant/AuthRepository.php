<?php

declare(strict_types=1);

namespace App\Repositories\Tenant;

use App\Models\Tenant\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Random\RandomException;

class AuthRepository
{
    private const int OTP_TTL = 15;

    private const string OTP_PREFIX = 'auth_otp';

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', strtolower(trim($email)))
            ->first();
    }

    /**
     * @throws RandomException
     */
    public function generateOtp(string $email, string $type): string
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put($this->otpKey($email, $type), $otp, now()->addMinutes(self::OTP_TTL));

        return $otp;
    }

    public function verifyOtp(string $email, string $otp, string $type): bool
    {
        $key = $this->otpKey($email, $type);
        $stored = Cache::get($key);

        if ($stored === null || $stored !== $otp) {
            return false;
        }

        Cache::forget($key);

        return true;
    }

    public function markEmailVerified(User $user): void
    {
        $user->update(['email_verified_at' => now()]);
    }

    public function updatePassword(User $user, string $plainPassword): void
    {
        $user->update(['password' => $plainPassword]);
    }

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

    public function validatePasswordResetToken(string $email, string $token): bool
    {
        $stored = Cache::get($this->passwordResetKey($email));

        return is_string($stored) && hash_equals($stored, $token);
    }

    public function forgetPasswordResetToken(string $email): void
    {
        Cache::forget($this->passwordResetKey($email));
    }

    public function isRateLimited(string $email, string $type): bool
    {
        $key = "otp_rate_limit:{$email}:{$type}";

        if (Cache::has($key)) {
            return true;
        }

        Cache::put($key, true, now()->addSeconds(60));

        return false;
    }

    private function passwordResetKey(string $email): string
    {
        return 'password_reset:'.md5(strtolower($email));
    }

    private function otpKey(string $email, string $type): string
    {
        return self::OTP_PREFIX.":{$type}:".md5(strtolower($email));
    }
}
