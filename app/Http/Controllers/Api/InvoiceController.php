<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Quote;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Invoice::query()
            ->with(['customer', 'user'])
            ->withCount('items')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('company_name', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->from_date, fn ($q, $date) => $q->where('invoice_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('invoice_date', '<=', $date))
            ->when($request->overdue, fn ($q) => $q->overdue())
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return InvoiceResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(InvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->create($request->validated());

        return response()->json([
            'message' => 'Invoice created successfully',
            'data' => new InvoiceResource($invoice->load(['customer', 'items.product'])),
        ], 201);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load(['customer', 'user', 'items.product', 'payments']));
    }

    public function update(InvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice = $this->invoiceService->update($invoice, $request->validated());

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => new InvoiceResource($invoice->load(['customer', 'items.product'])),
        ]);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully',
        ]);
    }

    public function send(Invoice $invoice): JsonResponse
    {
        $invoice->markAsSent();

        return response()->json([
            'message' => 'Invoice marked as sent',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    public function createFromQuote(Quote $quote): JsonResponse
    {
        if (!$quote->canBeConverted()) {
            return response()->json([
                'message' => 'Quote cannot be converted to invoice',
            ], 422);
        }

        $invoice = $this->invoiceService->createFromQuote($quote);

        return response()->json([
            'message' => 'Invoice created from quote successfully',
            'data' => new InvoiceResource($invoice->load(['customer', 'items.product'])),
        ], 201);
    }

    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->invoiceService->getStatistics(
            $request->from_date,
            $request->to_date
        );

        return response()->json(['data' => $stats]);
    }
}
