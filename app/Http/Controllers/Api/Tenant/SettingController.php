<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tenant;

use App\DTOs\Tenant\SettingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateSettingRequest;
use App\Contracts\Tenant\SettingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SettingController
 *
 * Handles tenant settings API endpoints.
 *
 * @package App\Http\Controllers\Tenant
 */
class SettingController extends Controller
{
    /**
     * SettingController constructor.
     *
     * @param SettingService $service
     */
    public function __construct(
        private readonly SettingServiceInterface $service
    ) {}

    /**
     * Get all tenant settings.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getAllSettings(),
        ]);
    }

    /**
     * Get branding settings only.
     *
     * @return JsonResponse
     */
    public function branding(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getBranding(),
        ]);
    }

    /**
     * Update tenant settings.
     *
     * @param UpdateSettingRequest $request
     * @return JsonResponse
     */
    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $dto = SettingDTO::fromRequest($request);
        $result = $this->service->updateSettings($dto);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Update branding only.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateBranding(Request $request): JsonResponse
    {
        $result = $this->service->updateBranding($request->all());

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Update payment settings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePayments(Request $request): JsonResponse
    {
        $result = $this->service->updatePayments($request->all());

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Toggle store active status.
     *
     * @return JsonResponse
     */
    public function toggleStatus(): JsonResponse
    {
        $result = $this->service->toggleStoreStatus();

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
