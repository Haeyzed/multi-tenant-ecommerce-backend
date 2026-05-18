<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\ChangePasswordRequest;
use App\Http\Requests\Central\Auth\ForgotPasswordRequest;
use App\Http\Requests\Central\Auth\LoginRequest;
use App\Http\Requests\Central\Auth\RegisterRequest;
use App\Http\Requests\Central\Auth\ResendVerificationOtpRequest;
use App\Http\Requests\Central\Auth\ResetPasswordRequest;
use App\Http\Requests\Central\Auth\VerifyOtpRequest;
use App\Http\Resources\Central\UserResource;
use App\Models\Central\User;
use App\Services\Central\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Random\RandomException;

/**
 * Class AuthController
 *
 * Handles central API authentication endpoints.
 *
 * @package App\Http\Controllers\Api\Central
 */
class AuthController extends Controller
{
    use ApiResponse; // Inject the trait here

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     */
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user account.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws RandomException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return $this->successResponse(
            'Registration successful. Please verify your email with the OTP sent.',
            new UserResource($user),
            201
        );
    }

    /**
     * Authenticate a user and generate a token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse('Login successful', [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ]);
    }

    /**
     * Request a password reset OTP.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws RandomException
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->forgotPassword($request->validated('email'));

        return $this->successResponse($result['message']);
    }

    /**
     * Verify an OTP for email verification or password reset.
     *
     * @param VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp($request->validated());

        return $this->successResponse($result['message'], $result);
    }

    /**
     * Resend the email verification OTP.
     *
     * @param ResendVerificationOtpRequest $request
     * @return JsonResponse
     * @throws RandomException
     */
    public function resendVerificationOtp(ResendVerificationOtpRequest $request): JsonResponse
    {
        $result = $this->authService->resendVerificationOtp($request->validated('email'));

        return $this->successResponse($result['message']);
    }

    /**
     * Reset the user's password using a valid token or OTP.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->resetPassword($request->validated());

        return $this->successResponse($result['message']);
    }

    /**
     * Change the password for the currently authenticated user.
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $result = $this->authService->changePassword($user, $request->validated());

        return $this->successResponse($result['message']);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me($request->user());

        return $this->successResponse('Profile retrieved successfully', new UserResource($user));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse('Logged out successfully.');
    }
}
