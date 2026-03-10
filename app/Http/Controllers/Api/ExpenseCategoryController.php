<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ExpenseCategory::query()
            ->withCount('expenses')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->roots_only, fn ($q) => $q->roots())
            ->orderBy($request->sort_by ?? 'name', $request->sort_order ?? 'asc');

        return ExpenseCategoryResource::collection($query->paginate($request->per_page ?? 50));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:expense_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = ExpenseCategory::create([
            ...$request->all(),
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new ExpenseCategoryResource($category),
        ], 201);
    }

    public function show(ExpenseCategory $expenseCategory): ExpenseCategoryResource
    {
        return new ExpenseCategoryResource($expenseCategory->load(['parent', 'children'])->loadCount('expenses'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:expense_categories,id', 'not_in:' . $expenseCategory->id],
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $expenseCategory->update([
            ...$request->all(),
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new ExpenseCategoryResource($expenseCategory),
        ]);
    }

    public function destroy(ExpenseCategory $expenseCategory): JsonResponse
    {
        if ($expenseCategory->expenses()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with existing expenses',
            ], 422);
        }

        if ($expenseCategory->children()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with subcategories',
            ], 422);
        }

        $expenseCategory->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
