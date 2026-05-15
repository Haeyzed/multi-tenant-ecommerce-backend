<?php

namespace App\Http\Controllers\Api\Central;

use App\Http\Controllers\Controller;
use App\Contracts\Central\InvoiceServiceInterface;
use App\Models\Central\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * @param InvoiceServiceInterface $invoiceService
     */
    public function __construct(
        private readonly InvoiceServiceInterface $invoiceService
    ) {}

    /**
     * Get all invoices.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $invoices = $this->invoiceService->getAllInvoices(
            $request->all(),
            $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices->items(),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    /**
     * Get pending invoices for a tenant.
     *
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function pending(Tenant $tenant): JsonResponse
    {
        $invoices = $this->invoiceService->getPendingInvoices($tenant);

        return response()->json([
            'success' => true,
            'message' => 'Pending invoices retrieved successfully',
            'data' => $invoices
        ]);
    }
}
