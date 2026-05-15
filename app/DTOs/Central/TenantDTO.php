<?php

namespace App\DTOs\Central;

use App\Enums\TenantStatus;

readonly class TenantDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $domain,
        public TenantStatus $status,
        public string $plan = 'basic',
        public ?array $data = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            domain: $data['domain'],
            status: TenantStatus::from($data['status'] ?? 'active'),
            plan: $data['plan'] ?? 'basic',
            data: $data['data'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'plan' => $this->plan,
            'data' => $this->data,
        ];
    }
}
