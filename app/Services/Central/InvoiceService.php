<?php

namespace App\Services\Central;

use App\Contracts\Central\InvoiceServiceInterface;
use App\Enums\InvoiceStatus;
use App\Models\Central\Invoice;
use App\Models\Central\Tenant;
use App\Models\Central\Plan;
use App\Repositories\Central\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class InvoiceService implements InvoiceServiceInterface
{
    /**
     * InvoiceService constructor.
     *
     * @param InvoiceRepository $repository
     */
    public function __construct(
        private InvoiceRepository $repository
    ) {}

    /**
     * Get all invoices.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->findAll($filters, $perPage);
    }

    /**
     * Generate an invoice for a tenant's subscription.
     *
     * @param Tenant $tenant
     * @param Plan $plan
     * @return Invoice
     */
    public function generateInvoiceForSubscription(Tenant $tenant, Plan $plan): Invoice
    {
        return $this->repository->create([
            'tenant_id' => $tenant->id,
            'amount' => $plan->price,
            'status' => InvoiceStatus::PENDING->value,
            'due_date' => Carbon::now()->addDays(7),
        ]);
    }

    /**
     * Mark an invoice as paid.
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function markAsPaid(Invoice $invoice): Invoice
    {
        return $this->repository->update($invoice, ['status' => InvoiceStatus::PAID->value]);
    }

    /**
     * Get all pending invoices for a tenant.
     *
     * @param Tenant $tenant
     * @return Collection
     */
    public function getPendingInvoices(Tenant $tenant): Collection
    {
        return $this->repository->getPendingInvoicesByTenant($tenant->id);
    }
}
