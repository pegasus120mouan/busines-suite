<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()
            ->with('supplier')
            ->when($request->search, fn ($q, $search) => $q->where('order_number', 'like', "%{$search}%")
                ->orWhereHas('supplier', fn ($q) => $q->where('company_name', 'like', "%{$search}%")))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc');

        $purchaseOrders = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();

        return view('purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $selectedSupplier = $request->supplier ? Supplier::find($request->supplier) : null;

        return view('purchase-orders.create', compact('suppliers', 'products', 'warehouses', 'selectedSupplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $purchaseOrder = DB::transaction(function () use ($validated) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $index => $item) {
                $product = Product::find($item['product_id']);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'description' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'sort_order' => $index,
                ]);
            }

            return $purchaseOrder;
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Bon de commande créé avec succès.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'user', 'warehouse']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return back()->with('error', 'Ce bon de commande ne peut plus être modifié.');
        }

        $purchaseOrder->load('items');
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products', 'warehouses'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return back()->with('error', 'Ce bon de commande ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $purchaseOrder->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                $product = Product::find($item['product_id']);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'description' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Bon de commande mis à jour.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Ce bon de commande ne peut pas être supprimé.');
        }

        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Bon de commande supprimé.');
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'draft') {
            $purchaseOrder->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return back()->with('success', 'Bon de commande marqué comme envoyé.');
    }

    public function confirm(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'sent') {
            $purchaseOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }

        return back()->with('success', 'Bon de commande confirmé par le fournisseur.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['sent', 'confirmed', 'partial'])) {
            return back()->with('error', 'Ce bon de commande ne peut pas être réceptionné.');
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.received_quantity' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $allReceived = true;
            $anyReceived = false;

            foreach ($validated['items'] as $itemData) {
                $item = PurchaseOrderItem::find($itemData['id']);
                $receivedQty = $itemData['received_quantity'];

                if ($receivedQty > 0) {
                    $anyReceived = true;
                    $item->increment('received_quantity', $receivedQty);

                    // Update stock
                    $stock = ProductStock::firstOrCreate(
                        [
                            'product_id' => $item->product_id,
                            'warehouse_id' => $purchaseOrder->warehouse_id,
                        ],
                        ['quantity' => 0, 'min_quantity' => 0]
                    );
                    
                    $quantityBefore = $stock->quantity;
                    $stock->increment('quantity', $receivedQty);

                    // Create stock movement
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $purchaseOrder->warehouse_id,
                        'user_id' => auth()->id(),
                        'type' => 'in',
                        'quantity' => $receivedQty,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $stock->quantity,
                        'reason' => 'Réception BC ' . $purchaseOrder->order_number,
                        'reference' => $purchaseOrder->order_number,
                    ]);
                }

                if ($item->received_quantity < $item->quantity) {
                    $allReceived = false;
                }
            }

            if ($anyReceived) {
                $purchaseOrder->update([
                    'status' => $allReceived ? 'received' : 'partial',
                    'received_at' => $allReceived ? now() : $purchaseOrder->received_at,
                ]);
            }
        });

        return back()->with('success', 'Réception enregistrée avec succès.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return back()->with('error', 'Ce bon de commande ne peut pas être annulé.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return back()->with('success', 'Bon de commande annulé.');
    }
}
