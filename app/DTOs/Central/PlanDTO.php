<?php

declare(strict_types=1);

namespace App\DTOs\Central;

use App\Models\Central\Plan;

readonly class PlanDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public ?array $features = [],
        public ?array $limits = [],
        public bool $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            features: $data['features'] ?? [],
            limits: $data['limits'] ?? [],
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    public static function fromUpdateRequest(array $data, Plan $plan): self
    {
        return new self(
            name: $data['name'] ?? $plan->name,
            price: array_key_exists('price', $data) ? (float) $data['price'] : (float) $plan->price,
            features: $data['features'] ?? $plan->features ?? [],
            limits: $data['limits'] ?? $plan->limits ?? [],
            isActive: array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $plan->is_active,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'features' => $this->features,
            'limits' => $this->limits,
            'is_active' => $this->isActive,
        ];
    }
}
