<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = PurchaseOrder::query()
            ->with(['supplier', 'warehouse', 'user'])
            ->withCount('items')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($q) => $q->where('company_name', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->warehouse_id, fn ($q, $id) => $q->where('warehouse_id', $id))
            ->when($request->from_date, fn ($q, $date) => $q->where('order_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('order_date', '<=', $date))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return PurchaseOrderResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(PurchaseOrderRequest $request): JsonResponse
    {
        $purchaseOrder = DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'user_id' => auth()->id(),
                'reference' => $request->reference,
                'order_date' => $request->order_date,
                'expected_date' => $request->expected_date,
                'discount_amount' => $request->discount_amount ?? 0,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'currency' => $request->currency ?? 'EUR',
                'notes' => $request->notes,
                'terms' => $request->terms,
                'status' => 'draft',
            ]);

            foreach ($request->items as $index => $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'unit',
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            return $po->fresh();
        });

        return response()->json([
            'message' => 'Purchase order created successfully',
            'data' => new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product'])),
        ], 201);
    }

    public function show(PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'warehouse', 'user', 'items.product']));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return response()->json([
                'message' => 'Cannot update purchase order in current status',
            ], 422);
        }

        $purchaseOrder = DB::transaction(function () use ($request, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'reference' => $request->reference,
                'order_date' => $request->order_date,
                'expected_date' => $request->expected_date,
                'discount_amount' => $request->discount_amount ?? 0,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'currency' => $request->currency ?? $purchaseOrder->currency,
                'notes' => $request->notes,
                'terms' => $request->terms,
            ]);

            $purchaseOrder->items()->delete();

            foreach ($request->items as $index => $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'unit',
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            return $purchaseOrder->fresh();
        });

        return response()->json([
            'message' => 'Purchase order updated successfully',
            'data' => new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product'])),
        ]);
    }

    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!in_array($purchaseOrder->status, ['draft', 'cancelled'])) {
            return response()->json([
                'message' => 'Cannot delete purchase order in current status',
            ], 422);
        }

        $purchaseOrder->delete();

        return response()->json([
            'message' => 'Purchase order deleted successfully',
        ]);
    }

    public function send(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->markAsSent();

        return response()->json([
            'message' => 'Purchase order marked as sent',
            'data' => new PurchaseOrderResource($purchaseOrder),
        ]);
    }

    public function confirm(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->markAsConfirmed();

        return response()->json([
            'message' => 'Purchase order confirmed',
            'data' => new PurchaseOrderResource($purchaseOrder),
        ]);
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.quantity_received' => ['required', 'numeric', 'min:0'],
        ]);

        if (!$purchaseOrder->warehouse_id) {
            return response()->json([
                'message' => 'No warehouse assigned to this purchase order',
            ], 422);
        }

        DB::transaction(function () use ($request, $purchaseOrder) {
            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::findOrFail($itemData['id']);

                if ($item->purchase_order_id !== $purchaseOrder->id) {
                    continue;
                }

                $quantityToReceive = min($itemData['quantity_received'], $item->remaining_quantity);

                if ($quantityToReceive > 0 && $item->product_id) {
                    $this->stockService->receiveStock(
                        $item->product,
                        $purchaseOrder->warehouse,
                        (int) $quantityToReceive,
                        $item->unit_price,
                        $purchaseOrder->order_number,
                        "Received from PO #{$purchaseOrder->order_number}"
                    );
                }

                $item->increment('quantity_received', $quantityToReceive);
            }

            if ($purchaseOrder->isFullyReceived()) {
                $purchaseOrder->markAsReceived();
            } else {
                $purchaseOrder->update(['status' => 'partial']);
            }
        });

        return response()->json([
            'message' => 'Items received successfully',
            'data' => new PurchaseOrderResource($purchaseOrder->fresh()->load(['items.product'])),
        ]);
    }
}
