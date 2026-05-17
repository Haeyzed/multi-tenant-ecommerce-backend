<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tenant;

use App\Contracts\Tenant\AuthServiceInterface;
use App\DTOs\Tenant\Auth\ChangePasswordDTO;
use App\DTOs\Tenant\Auth\ForgotPasswordDTO;
use App\DTOs\Tenant\Auth\LoginDTO;
use App\DTOs\Tenant\Auth\ResetPasswordDTO;
use App\DTOs\Tenant\Auth\VerifyOtpDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\ChangePasswordRequest;
use App\Http\Requests\Tenant\Auth\ForgotPasswordRequest;
use App\Http\Requests\Tenant\Auth\LoginRequest;
use App\Http\Requests\Tenant\Auth\RegisterRequest;
use App\Http\Requests\Tenant\Auth\ResendVerificationOtpRequest;
use App\Http\Requests\Tenant\Auth\ResetPasswordRequest;
use App\Http\Requests\Tenant\Auth\VerifyOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {}

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

    public function resendVerificationOtp(ResendVerificationOtpRequest $request): JsonResponse
    {
        $result = $this->authService->resendVerificationOtp($request->validated('email'));

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

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

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $result = $this->authService->changePassword(
            $request->user(),
            ChangePasswordDTO::fromRequest($request->validated())
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->authService->me($request->user()->load('roles', 'permissions')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}
