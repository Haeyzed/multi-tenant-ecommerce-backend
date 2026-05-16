<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\AuthServiceInterface;
use App\DTOs\Central\Auth\ChangePasswordDTO;
use App\DTOs\Central\Auth\ForgotPasswordDTO;
use App\DTOs\Central\Auth\LoginDTO;
use App\DTOs\Central\Auth\ResetPasswordDTO;
use App\DTOs\Central\Auth\VerifyOtpDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\ChangePasswordRequest;
use App\Http\Requests\Central\Auth\ForgotPasswordRequest;
use App\Http\Requests\Central\Auth\LoginRequest;
use App\Http\Requests\Central\Auth\RegisterRequest;
use App\Http\Requests\Central\Auth\ResetPasswordRequest;
use App\Http\Requests\Central\Auth\VerifyOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 *
 * Handles authentication API endpoints for central platform users.
 *
 * @package App\Http\Controllers\Api\Central
 */
class AuthController extends Controller
{
    /**
     * AuthController constructor.
     *
     * @param AuthServiceInterface $authService Authentication service instance
     */
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request Validated registration data
     * @return JsonResponse Created user with verification prompt (HTTP 201)
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
        ], 201);
    }

    /**
     * Authenticate user and issue token.
     *
     * @param LoginRequest $request Validated login credentials
     * @return JsonResponse Authentication token and user data
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            LoginDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => $result,
        ]);
    }

    /**
     * Request password reset OTP.
     *
     * @param ForgotPasswordRequest $request Validated email
     * @return JsonResponse OTP sent confirmation
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->forgotPassword(
            ForgotPasswordDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * Verify OTP code.
     *
     * @param VerifyOtpRequest $request Validated OTP data
     * @return JsonResponse Verification result
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            VerifyOtpDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result,
        ]);
    }

    /**
     * Reset password with verified OTP.
     *
     * @param ResetPasswordRequest $request Validated reset data
     * @return JsonResponse Password reset confirmation
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->resetPassword(
            ResetPasswordDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * Change the password for an authenticated user.
     *
     * @param ChangePasswordRequest $request Validated password change
     * @return JsonResponse Password change confirmation
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $result = $this->authService->changePassword(
            Auth::user(),
            ChangePasswordDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * Get an authenticated user profile.
     *
     * @param Request $request
     * @return JsonResponse User profile with roles and permissions
     */
    public function me(Request $request): JsonResponse
    {
        $result = $this->authService->me($request->user());

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Log out user and revoke the token.
     *
     * @param Request $request
     * @return JsonResponse Logout confirmation
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}
