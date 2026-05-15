<?php

namespace App\DTOs\Central;

use App\Enums\SubscriptionStatus;
use Carbon\Carbon;

readonly class SubscriptionDTO
{
    /**
     * @param string $tenantId
     * @param int $planId
     * @param SubscriptionStatus $status
     * @param Carbon|null $startsAt
     * @param Carbon|null $endsAt
     */
    public function __construct(
        public string $tenantId,
        public int $planId,
        public SubscriptionStatus $status = SubscriptionStatus::ACTIVE,
        public ?Carbon $startsAt = null,
        public ?Carbon $endsAt = null,
    ) {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            tenantId: $data['tenant_id'],
            planId: (int) $data['plan_id'],
            status: isset($data['status']) ? SubscriptionStatus::from($data['status']) : SubscriptionStatus::ACTIVE,
            startsAt: isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : Carbon::now(),
            endsAt: isset($data['ends_at']) ? Carbon::parse($data['ends_at']) : Carbon::now()->addMonth(),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'plan_id' => $this->planId,
            'status' => $this->status,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
        ];
    }
}
