<?php

declare(strict_types=1);

namespace App\DTOs\Central;

use App\Enums\Central\TenantStatus;

readonly class UpdateTenantDTO
{
    /**
     * @param array<string, mixed>|null $data
     */
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $domain = null,
        public ?TenantStatus $status = null,
        public ?int $planId = null,
        public ?array $data = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: array_key_exists('phone', $data) ? $data['phone'] : null,
            domain: isset($data['domain']) ? strtolower(trim($data['domain'])) : null,
            status: isset($data['status']) ? TenantStatus::from($data['status']) : null,
            planId: isset($data['plan_id']) ? (int) $data['plan_id'] : null,
            data: $data['data'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = [];

        if ($this->name !== null) {
            $attributes['name'] = $this->name;
        }

        if ($this->email !== null) {
            $attributes['email'] = $this->email;
        }

        if ($this->phone !== null) {
            $attributes['phone'] = $this->phone;
        }

        if ($this->status !== null) {
            $attributes['status'] = $this->status;
        }

        if ($this->planId !== null) {
            $attributes['plan_id'] = $this->planId;
        }

        if ($this->data !== null) {
            $attributes['data'] = $this->data;
        }

        return $attributes;
    }

    public function hasDomain(): bool
    {
        return $this->domain !== null;
    }
}
