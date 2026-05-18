<?php

declare(strict_types=1);

namespace App\DTOs\Central;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PlanData extends Data
{
    public function __construct(
        public int|Optional $id,
        public string $name,
        public float $price,
        public ?array $features = [],
        public ?array $limits = [],
        #[MapInputName('is_active')]
        #[MapOutputName('is_active')]
        public bool $isActive = true,
        public Carbon|Optional $created_at,
        public Carbon|Optional $updated_at,
    ) {}

    /**
     * Intercept data before DTO instantiation to fix double-encoded JSON
     * or raw string issues from the database or array merges.
     *
     * @param array<string, mixed> $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        // Force decode features if it comes in as a string
        if (isset($properties['features']) && is_string($properties['features'])) {
            $decoded = json_decode($properties['features'], true);
            $properties['features'] = is_array($decoded) ? $decoded : [];
        }

        // Force decode limits if it comes in as a string
        if (isset($properties['limits']) && is_string($properties['limits'])) {
            $decoded = json_decode($properties['limits'], true);
            $properties['limits'] = is_array($decoded) ? $decoded : [];
        }

        return $properties;
    }
}
