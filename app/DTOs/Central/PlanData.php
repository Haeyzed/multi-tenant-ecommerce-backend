<?php

declare(strict_types=1);

namespace App\DTOs\Central;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

class PlanData extends Data
{
    public function __construct(
        public string $name,
        public float $price,
        public ?array $features = [],
        public ?array $limits = [],
        #[MapInputName('is_active')]
        #[MapOutputName('is_active')]
        public bool $isActive = true,
    ) {}
}
