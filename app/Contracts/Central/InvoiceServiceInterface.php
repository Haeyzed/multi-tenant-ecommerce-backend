<?php

namespace App\Contracts\Central;

use App\DTOs\Central\InvoiceDTO;
use App\Models\Central\Invoice;
use App\Models\Central\Tenant;
use App\Models\Central\Plan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceServiceInterface
{
    /**
     * Get all invoices.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Generate an invoice for a tenant's subscription.
     *
     * @param Tenant $tenant
     * @param Plan $plan
     * @return Invoice
     */
    public function generateInvoiceForSubscription(Tenant $tenant, Plan $plan): Invoice;

    /**
     * Mark an invoice as paid.
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function markAsPaid(Invoice $invoice): Invoice;

    /**
     * Get all pending invoices for a tenant.
     *
     * @param Tenant $tenant
     * @return Collection
     */
    public function getPendingInvoices(Tenant $tenant): Collection;
}
