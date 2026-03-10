<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        
        $query = ProductStock::query()
            ->with(['product', 'warehouse'])
            ->when($request->warehouse_id, fn ($q, $id) => $q->where('warehouse_id', $id))
            ->when($request->search, fn ($q, $search) => $q->whereHas('product', fn ($q) => 
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
            ->when($request->low_stock, fn ($q) => $q->whereRaw('quantity <= min_quantity'))
            ->orderBy('quantity', 'asc');

        $stocks = $query->paginate(20)->withQueryString();

        return view('stock.index', compact('stocks', 'warehouses'));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::query()
            ->with(['product', 'warehouse', 'user'])
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->warehouse_id, fn ($q, $id) => $q->where('warehouse_id', $id))
            ->when($request->product_id, fn ($q, $id) => $q->where('product_id', $id))
            ->when($request->start_date, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->end_date, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy('created_at', 'desc');

        $movements = $query->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('track_stock', true)->orderBy('name')->get();

        return view('stock.movements', compact('movements', 'warehouses', 'products'));
    }

    public function adjustForm()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('track_stock', true)->where('is_active', true)->orderBy('name')->get();

        return view('stock.adjust', compact('warehouses', 'products'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:500'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($validated) {
            $stock = ProductStock::firstOrCreate(
                [
                    'product_id' => $validated['product_id'],
                    'warehouse_id' => $validated['warehouse_id'],
                ],
                ['quantity' => 0, 'min_quantity' => 0]
            );

            $quantityChange = $validated['type'] === 'out' ? -$validated['quantity'] : $validated['quantity'];
            
            if ($validated['type'] === 'adjustment') {
                $quantityChange = $validated['quantity'] - $stock->quantity;
            }

            $stock->increment('quantity', $quantityChange);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'quantity' => abs($quantityChange),
                'quantity_before' => $stock->quantity - $quantityChange,
                'quantity_after' => $stock->quantity,
                'reason' => $validated['reason'],
                'reference' => $validated['reference'],
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Stock ajusté avec succès.');
    }

    public function transferForm()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('track_stock', true)->where('is_active', true)->orderBy('name')->get();

        return view('stock.transfer', compact('warehouses', 'products'));
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'exists:warehouses,id', 'different:from_warehouse_id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $fromStock = ProductStock::where('product_id', $validated['product_id'])
            ->where('warehouse_id', $validated['from_warehouse_id'])
            ->first();

        if (!$fromStock || $fromStock->quantity < $validated['quantity']) {
            return back()->with('error', 'Stock insuffisant dans l\'entrepôt source.')->withInput();
        }

        DB::transaction(function () use ($validated, $fromStock) {
            $fromStock->decrement('quantity', $validated['quantity']);

            $toStock = ProductStock::firstOrCreate(
                [
                    'product_id' => $validated['product_id'],
                    'warehouse_id' => $validated['to_warehouse_id'],
                ],
                ['quantity' => 0, 'min_quantity' => 0]
            );
            $toStock->increment('quantity', $validated['quantity']);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['from_warehouse_id'],
                'user_id' => auth()->id(),
                'type' => 'transfer_out',
                'quantity' => $validated['quantity'],
                'quantity_before' => $fromStock->quantity + $validated['quantity'],
                'quantity_after' => $fromStock->quantity,
                'reason' => $validated['reason'] ?? 'Transfert vers ' . Warehouse::find($validated['to_warehouse_id'])->name,
                'reference' => 'TRF-' . now()->format('YmdHis'),
            ]);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['to_warehouse_id'],
                'user_id' => auth()->id(),
                'type' => 'transfer_in',
                'quantity' => $validated['quantity'],
                'quantity_before' => $toStock->quantity - $validated['quantity'],
                'quantity_after' => $toStock->quantity,
                'reason' => $validated['reason'] ?? 'Transfert depuis ' . Warehouse::find($validated['from_warehouse_id'])->name,
                'reference' => 'TRF-' . now()->format('YmdHis'),
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Transfert effectué avec succès.');
    }
}
