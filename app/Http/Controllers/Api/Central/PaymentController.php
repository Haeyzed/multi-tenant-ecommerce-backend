<?php

namespace App\Http\Controllers\Api\Central;

use App\Contracts\Central\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ProcessPaymentRequest;
use App\Models\Central\Invoice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @param PaymentServiceInterface $paymentService
     */
    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {}

    /**
     * Get all payments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $payments = $this->paymentService->getAllPayments(
            $request->all(),
            $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Payments retrieved successfully',
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    /**
     * Process a payment for an invoice.
     *
     * @param ProcessPaymentRequest $request
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function process(ProcessPaymentRequest $request, Invoice $invoice): JsonResponse
    {
        try {
            $validated = $request->validated();
            $payment = $this->paymentService->processPayment(
                $invoice,
                $validated['payment_method'],
                $validated['transaction_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $payment
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
