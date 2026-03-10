<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->with(['category', 'supplier'])
            ->withSum('stocks', 'quantity')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            }))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->low_stock, fn ($q) => $q->lowStock())
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return ProductResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource($product->load(['category', 'supplier'])),
        ], 201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['category', 'supplier', 'stocks.warehouse']));
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product->load(['category', 'supplier'])),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    public function lowStock(Request $request): AnonymousResourceCollection
    {
        $products = Product::lowStock()
            ->with(['category', 'supplier'])
            ->withSum('stocks', 'quantity')
            ->paginate($request->per_page ?? 15);

        return ProductResource::collection($products);
    }
}
