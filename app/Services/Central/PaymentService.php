<?php

namespace App\Services\Central;

use App\Contracts\Central\InvoiceServiceInterface;
use App\Contracts\Central\PaymentServiceInterface;
use App\Enums\InvoiceStatus;
use App\Models\Central\Payment;
use App\Models\Central\Invoice;
use App\Repositories\Central\PaymentRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class PaymentService implements PaymentServiceInterface
{
    /**
     * PaymentService constructor.
     *
     * @param InvoiceServiceInterface $invoiceService
     * @param PaymentRepository $repository
     */
    public function __construct(
        private InvoiceServiceInterface $invoiceService,
        private PaymentRepository $repository
    ) {}

    /**
     * Get all payments.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPayments(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Process a payment for an invoice.
     *
     * @param Invoice $invoice
     * @param string $paymentMethod
     * @param string|null $transactionId
     * @return Payment
     * @throws Exception
     */
    public function processPayment(Invoice $invoice, string $paymentMethod, ?string $transactionId = null): Payment
    {
        if ($invoice->status === InvoiceStatus::PAID->value || $invoice->status === InvoiceStatus::PAID) {
            throw new Exception("Invoice is already paid.");
        }

        return DB::transaction(function () use ($invoice, $paymentMethod, $transactionId) {
            $payment = $this->repository->create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->amount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
            ]);

            $this->invoiceService->markAsPaid($invoice);

            return $payment;
        });
    }
}
