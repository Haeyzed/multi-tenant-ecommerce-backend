<?php

namespace App\Repositories\Central;

use App\Models\Central\Payment;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentRepository
{
    /**
     * Get all payments with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Payment::class)
            ->allowedFilters(
                AllowedFilter::exact('invoice_id'),
                AllowedFilter::exact('payment_method'),
                AllowedFilter::exact('status'),
            )
            ->allowedSorts('amount', 'created_at', 'status')
            ->with('invoice')
            ->paginate($perPage);
    }

    /**
     * Create a new payment.
     *
     * @param array $data
     * @return Payment
     */
    public function create(array $data): Payment
    {
        return Payment::query()->create($data);
    }
}