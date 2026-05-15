<?php

namespace App\DTOs\Central;

readonly class PaymentDTO
{
    /**
     * @param int $invoiceId
     * @param float $amount
     * @param string $paymentMethod
     * @param string|null $transactionId
     */
    public function __construct(
        public int $invoiceId,
        public float $amount,
        public string $paymentMethod,
        public ?string $transactionId = null,
    ) {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            invoiceId: (int) $data['invoice_id'],
            amount: (float) $data['amount'],
            paymentMethod: $data['payment_method'],
            transactionId: $data['transaction_id'] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'invoice_id' => $this->invoiceId,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'transaction_id' => $this->transactionId,
        ];
    }
}
