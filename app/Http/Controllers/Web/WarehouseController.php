<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::query()
            ->withCount('stocks')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('name');

        $warehouses = $query->paginate(15)->withQueryString();

        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:warehouses,code'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);

        if ($validated['is_default']) {
            Warehouse::where('is_default', true)->update(['is_default' => false]);
        }

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'Entrepôt créé avec succès.');
    }

    public function show(Warehouse $warehouse)
    {
        $stocks = ProductStock::with('product')
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->paginate(20);

        $totalValue = $stocks->sum(fn ($s) => $s->quantity * ($s->product->sale_price ?? 0));

        return view('warehouses.show', compact('warehouse', 'stocks', 'totalValue'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:warehouses,code,' . $warehouse->id],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);

        if ($validated['is_default'] && !$warehouse->is_default) {
            Warehouse::where('is_default', true)->update(['is_default' => false]);
        }

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Entrepôt mis à jour.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->stocks()->where('quantity', '>', 0)->exists()) {
            return back()->with('error', 'Impossible de supprimer cet entrepôt car il contient du stock.');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Entrepôt supprimé.');
    }
}
