<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\SettingServiceInterface;
use App\DTOs\Central\SettingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\UpdateSettingRequest;
use App\Http\Resources\Central\SettingResource;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settingService,
    ) {}

    public function show(): JsonResponse
    {
        $settings = $this->settingService->getSettings();
        $settings->load('defaultPlan');

        return response()->json([
            'success' => true,
            'data' => new SettingResource($settings),
        ]);
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $settings = $this->settingService->updateSettings(
            SettingDTO::fromRequest($request->validated())
        );
        $settings->load('defaultPlan');

        return response()->json([
            'success' => true,
            'message' => 'Platform settings updated successfully',
            'data' => new SettingResource($settings),
        ]);
    }

    public function toggleMaintenance(): JsonResponse
    {
        $settings = $this->settingService->toggleMaintenanceMode();
        $settings->load('defaultPlan');

        return response()->json([
            'success' => true,
            'message' => $settings->maintenance_mode
                ? 'Maintenance mode enabled'
                : 'Maintenance mode disabled',
            'data' => new SettingResource($settings),
        ]);
    }
}
