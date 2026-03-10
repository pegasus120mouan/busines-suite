<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Payment::query()
            ->with(['invoice.customer', 'user'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->method, fn ($q, $method) => $q->where('method', $method))
            ->when($request->invoice_id, fn ($q, $id) => $q->where('invoice_id', $id))
            ->when($request->from_date, fn ($q, $date) => $q->where('payment_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('payment_date', '<=', $date))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return PaymentResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(PaymentRequest $request): JsonResponse
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($request->amount > $invoice->balance_due) {
            return response()->json([
                'message' => 'Payment amount exceeds balance due',
            ], 422);
        }

        $payment = Payment::create([
            'tenant_id' => auth()->user()->tenant_id,
            'invoice_id' => $request->invoice_id,
            'user_id' => auth()->id(),
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'status' => 'completed',
        ]);

        return response()->json([
            'message' => 'Payment recorded successfully',
            'data' => new PaymentResource($payment->load('invoice')),
        ], 201);
    }

    public function show(Payment $payment): PaymentResource
    {
        return new PaymentResource($payment->load(['invoice.customer', 'user']));
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully',
        ]);
    }
}
