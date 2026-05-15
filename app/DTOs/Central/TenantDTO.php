<?php

namespace App\DTOs\Central;

use App\Enums\Central\TenantStatus;

readonly class TenantDTO
{

    /**
     * @param string $name
     * @param string $email
     * @param string|null $phone
     * @param string $domain
     * @param TenantStatus $status
     * @param int $planId
     * @param array $data
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $domain,
        public TenantStatus $status,
        public int $planId,
        public array $data = [],
    ) {}

    /**
     *
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            domain: $data['domain'],
            status: TenantStatus::from($data['status'] ?? 'active'),
            planId: (int) $data['plan_id'],
            data: $data['data'] ?? [],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'plan_id' => $this->planId,
            'data' => $this->data,
        ];
    }
}
