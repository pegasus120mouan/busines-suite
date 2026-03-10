<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductStockResource;
use App\Http\Resources\StockMovementResource;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ProductStock::query()
            ->with(['product', 'warehouse'])
            ->when($request->warehouse_id, fn ($q, $id) => $q->where('warehouse_id', $id))
            ->when($request->product_id, fn ($q, $id) => $q->where('product_id', $id))
            ->when($request->low_stock, fn ($q) => $q->whereRaw('quantity <= (SELECT min_stock_level FROM products WHERE products.id = product_stocks.product_id)'))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return ProductStockResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function movements(Request $request): AnonymousResourceCollection
    {
        $query = StockMovement::query()
            ->with(['product', 'warehouse', 'user'])
            ->when($request->warehouse_id, fn ($q, $id) => $q->where('warehouse_id', $id))
            ->when($request->product_id, fn ($q, $id) => $q->where('product_id', $id))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->from_date, fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('created_at', '<=', $date))
            ->orderBy('created_at', 'desc');

        return StockMovementResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function adjust(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $warehouse = Warehouse::findOrFail($request->warehouse_id);

        $movement = $this->stockService->adjustStock(
            $product,
            $warehouse,
            $request->quantity,
            $request->reason
        );

        return response()->json([
            'message' => 'Stock adjusted successfully',
            'data' => new StockMovementResource($movement->load(['product', 'warehouse'])),
        ]);
    }

    public function transfer(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'exists:warehouses,id', 'different:from_warehouse_id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $fromWarehouse = Warehouse::findOrFail($request->from_warehouse_id);
        $toWarehouse = Warehouse::findOrFail($request->to_warehouse_id);

        $movements = $this->stockService->transferStock(
            $product,
            $fromWarehouse,
            $toWarehouse,
            $request->quantity,
            $request->reason
        );

        return response()->json([
            'message' => 'Stock transferred successfully',
            'data' => StockMovementResource::collection($movements),
        ]);
    }

    public function receive(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:100'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $warehouse = Warehouse::findOrFail($request->warehouse_id);

        $movement = $this->stockService->receiveStock(
            $product,
            $warehouse,
            $request->quantity,
            $request->unit_cost,
            $request->reference,
            $request->reason
        );

        return response()->json([
            'message' => 'Stock received successfully',
            'data' => new StockMovementResource($movement->load(['product', 'warehouse'])),
        ]);
    }
}
