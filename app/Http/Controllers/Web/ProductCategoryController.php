<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::query()
            ->withCount('products')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('product-categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = ProductCategory::whereNull('parent_id')->orderBy('name')->get();
        return view('product-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:product_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(ProductCategory $productCategory)
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->where('id', '!=', $productCategory->id)
            ->orderBy('name')
            ->get();

        return view('product-categories.edit', compact('productCategory', 'categories'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:product_categories,id', 'not_in:' . $productCategory->id],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->products()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')->with('success', 'Catégorie supprimée.');
    }
}
