<?php

namespace App\DTOs\Central;

use App\Enums\InvoiceStatus;
use Carbon\Carbon;

readonly class InvoiceDTO
{
    /**
     * @param string $tenantId
     * @param float $amount
     * @param InvoiceStatus $status
     * @param Carbon|null $dueDate
     */
    public function __construct(
        public string $tenantId,
        public float $amount,
        public InvoiceStatus $status = InvoiceStatus::PENDING,
        public ?Carbon $dueDate = null,
    ) {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            tenantId: $data['tenant_id'],
            amount: (float) $data['amount'],
            status: isset($data['status']) ? InvoiceStatus::from($data['status']) : InvoiceStatus::PENDING,
            dueDate: isset($data['due_date']) ? Carbon::parse($data['due_date']) : Carbon::now()->addDays(7),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'amount' => $this->amount,
            'status' => $this->status,
            'due_date' => $this->dueDate,
        ];
    }
}
