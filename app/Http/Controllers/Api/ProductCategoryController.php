<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ProductCategory::query()
            ->withCount('products')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->roots_only, fn ($q) => $q->roots())
            ->when($request->parent_id, fn ($q, $id) => $q->where('parent_id', $id))
            ->orderBy($request->sort_by ?? 'sort_order', $request->sort_order ?? 'asc');

        return ProductCategoryResource::collection($query->paginate($request->per_page ?? 50));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = ProductCategory::create([
            ...$request->all(),
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new ProductCategoryResource($category),
        ], 201);
    }

    public function show(ProductCategory $productCategory): ProductCategoryResource
    {
        return new ProductCategoryResource($productCategory->load(['parent', 'children'])->loadCount('products'));
    }

    public function update(Request $request, ProductCategory $productCategory): JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:product_categories,id', 'not_in:' . $productCategory->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $productCategory->update([
            ...$request->all(),
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new ProductCategoryResource($productCategory),
        ]);
    }

    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        if ($productCategory->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with existing products',
            ], 422);
        }

        if ($productCategory->children()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with subcategories',
            ], 422);
        }

        $productCategory->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    public function tree(): JsonResponse
    {
        $categories = ProductCategory::roots()
            ->with(['children' => fn ($q) => $q->with('children')])
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => ProductCategoryResource::collection($categories),
        ]);
    }
}
