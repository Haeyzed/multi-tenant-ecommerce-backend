<?php

namespace App\Contracts\Central;

use App\DTOs\Central\PaymentDTO;
use App\Models\Central\Payment;
use App\Models\Central\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentServiceInterface
{
    /**
     * Get all payments.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPayments(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Process a payment for an invoice.
     *
     * @param Invoice $invoice
     * @param string $paymentMethod
     * @param string|null $transactionId
     * @return Payment
     */
    public function processPayment(Invoice $invoice, string $paymentMethod, ?string $transactionId = null): Payment;
}
