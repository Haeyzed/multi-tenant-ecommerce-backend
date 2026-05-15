<?php

namespace App\DTOs\Central;

readonly class PlanDTO
{
    /**
     * @param string $name
     * @param float $price
     * @param array|null $features
     * @param array|null $limits
     */
    public function __construct(
        public string $name,
        public float $price,
        public ?array $features = [],
        public ?array $limits = [],
    ) {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            features: $data['features'] ?? [],
            limits: $data['limits'] ?? [],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'features' => $this->features,
            'limits' => $this->limits,
        ];
    }
}
