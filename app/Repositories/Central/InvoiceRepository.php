<?php

namespace App\Repositories\Central;

use App\Enums\InvoiceStatus;
use App\Models\Central\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InvoiceRepository
{
    /**
     * Get all invoices with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Invoice::class)
            ->allowedFilters([
                AllowedFilter::exact('tenant_id'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts('amount', 'due_date', 'status', 'created_at')
            ->with('tenant')
            ->paginate($perPage);
    }

    /**
     * Create a new invoice.
     *
     * @param array $data
     * @return Invoice
     */
    public function create(array $data): Invoice
    {
        return Invoice::query()->create($data);
    }

    /**
     * Update an existing invoice.
     *
     * @param Invoice $invoice
     * @param array $data
     * @return Invoice
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice;
    }

    /**
     * Get pending invoices for a tenant.
     *
     * @param string $tenantId
     * @return Collection
     */
    public function getPendingInvoicesByTenant(string $tenantId): Collection
    {
        return Invoice::query()->where('tenant_id', $tenantId)
            ->where('status', InvoiceStatus::PENDING->value)
            ->get();
    }
}
