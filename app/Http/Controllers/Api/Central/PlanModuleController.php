<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PlanModuleController extends Controller
{
    /**
     * List available plan feature modules and limit keys for plan builders.
     */
    public function index(): JsonResponse
    {
        $modules = config('plan_modules.modules', []);
        $limitKeys = config('plan_modules.limit_keys', []);

        $data = collect($modules)->map(fn (array $meta, string $key) => [
            'key' => $key,
            'label' => $meta['label'] ?? $key,
            'description' => $meta['description'] ?? null,
        ])->values();

        return response()->json([
            'success' => true,
            'message' => 'Plan modules retrieved successfully',
            'data' => [
                'modules' => $data,
                'limit_keys' => $limitKeys,
            ],
        ]);
    }
}
