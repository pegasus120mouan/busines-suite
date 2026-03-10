<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuoteRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteService $quoteService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Quote::query()
            ->with(['customer', 'user'])
            ->withCount('items')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('company_name', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->from_date, fn ($q, $date) => $q->where('quote_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('quote_date', '<=', $date))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return QuoteResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(QuoteRequest $request): JsonResponse
    {
        $quote = $this->quoteService->create($request->validated());

        return response()->json([
            'message' => 'Quote created successfully',
            'data' => new QuoteResource($quote->load(['customer', 'items.product'])),
        ], 201);
    }

    public function show(Quote $quote): QuoteResource
    {
        return new QuoteResource($quote->load(['customer', 'user', 'items.product', 'invoice']));
    }

    public function update(QuoteRequest $request, Quote $quote): JsonResponse
    {
        $quote = $this->quoteService->update($quote, $request->validated());

        return response()->json([
            'message' => 'Quote updated successfully',
            'data' => new QuoteResource($quote->load(['customer', 'items.product'])),
        ]);
    }

    public function destroy(Quote $quote): JsonResponse
    {
        $quote->delete();

        return response()->json([
            'message' => 'Quote deleted successfully',
        ]);
    }

    public function send(Quote $quote): JsonResponse
    {
        $quote->markAsSent();

        return response()->json([
            'message' => 'Quote marked as sent',
            'data' => new QuoteResource($quote),
        ]);
    }

    public function accept(Quote $quote): JsonResponse
    {
        $quote->markAsAccepted();

        return response()->json([
            'message' => 'Quote accepted',
            'data' => new QuoteResource($quote),
        ]);
    }

    public function reject(Quote $quote): JsonResponse
    {
        $quote->markAsRejected();

        return response()->json([
            'message' => 'Quote rejected',
            'data' => new QuoteResource($quote),
        ]);
    }
}
