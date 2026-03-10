<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'supplier'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            }))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc');

        $products = $query->paginate(15)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:product,service'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products')->where('tenant_id', auth()->user()->tenant_id)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'unit' => ['nullable', 'string', 'max:20'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'track_stock' => ['nullable', 'boolean'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['track_stock'] = $request->boolean('track_stock');
        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produit créé avec succès.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'stocks.warehouse']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:product,service'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products')->where('tenant_id', auth()->user()->tenant_id)->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'unit' => ['nullable', 'string', 'max:20'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'track_stock' => ['nullable', 'boolean'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['track_stock'] = $request->boolean('track_stock');
        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $product)
    {
        if ($product->invoiceItems()->exists() || $product->quoteItems()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce produit car il est utilisé dans des factures ou devis.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé avec succès.');
    }
}
