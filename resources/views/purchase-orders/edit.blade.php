@extends('layouts.dashboard')

@section('title', 'Modifier BC ' . $purchaseOrder->order_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $purchaseOrder->order_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $purchaseOrder->supplier->company_name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" class="space-y-6" id="poForm">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur *</label>
                    <select name="supplier_id" id="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Entrepôt de réception *</label>
                    <select name="warehouse_id" id="warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $purchaseOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Date de commande *</label>
                    <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $purchaseOrder->order_date->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>

                <div>
                    <label for="expected_date" class="block text-sm font-medium text-gray-700 mb-1">Date de livraison prévue</label>
                    <input type="date" name="expected_date" id="expected_date" value="{{ old('expected_date', $purchaseOrder->expected_date?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Articles</h2>
                <button type="button" onclick="addItem()" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm bg-primary-50 text-primary-700 font-medium rounded-lg hover:bg-primary-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Ajouter une ligne
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full" id="itemsTable">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Quantité</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Prix unitaire</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-24">TVA %</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Total HT</th>
                            <th class="pb-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($purchaseOrder->items as $index => $item)
                        <tr class="item-row border-b border-gray-100">
                            <td class="py-3 pr-2">
                                <select name="items[{{ $index }}][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? $product->sale_price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-3 px-2">
                                <input type="number" name="items[{{ $index }}][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500" value="{{ $item->quantity }}" min="0.01" step="0.01" required>
                            </td>
                            <td class="py-3 px-2">
                                <input type="number" name="items[{{ $index }}][unit_price]" class="price-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500" value="{{ $item->unit_price }}" min="0" step="0.01" required>
                            </td>
                            <td class="py-3 px-2">
                                <input type="number" name="items[{{ $index }}][tax_rate]" class="tax-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500" value="{{ $item->tax_rate }}" min="0" max="100" step="0.01">
                            </td>
                            <td class="py-3 px-2 text-right">
                                <span class="line-total font-medium">{{ number_format($item->subtotal, 0, ',', ' ') }}</span>
                            </td>
                            <td class="py-3 pl-2">
                                <button type="button" onclick="removeItem(this)" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Sous-total HT</span>
                        <span id="subtotal" class="font-medium">{{ number_format($purchaseOrder->subtotal, 0, ',', ' ') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">TVA</span>
                        <span id="taxTotal" class="font-medium">{{ number_format($purchaseOrder->tax_amount, 0, ',', ' ') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                        <span>Total TTC</span>
                        <span id="grandTotal">{{ number_format($purchaseOrder->total, 0, ',', ' ') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
            <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $purchaseOrder->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">Enregistrer</button>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ count($purchaseOrder->items) }};

function addItem() {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.className = 'item-row border-b border-gray-100';
    row.innerHTML = `
        <td class="py-3 pr-2">
            <select name="items[${itemIndex}][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" required>
                <option value="">Sélectionner</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? $product->sale_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                @endforeach
            </select>
        </td>
        <td class="py-3 px-2">
            <input type="number" name="items[${itemIndex}][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500" value="1" min="0.01" step="0.01" required>
        </td>
        <td class="py-3 px-2">
            <input type="number" name="items[${itemIndex}][unit_price]" class="price-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500" value="0" min="0" step="0.01" required>
        </td>
        <td class="py-3 px-2">
            <input type="number" name="items[${itemIndex}][tax_rate]" class="tax-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500" value="20" min="0" max="100" step="0.01">
        </td>
        <td class="py-3 px-2 text-right">
            <span class="line-total font-medium">0</span>
        </td>
        <td class="py-3 pl-2">
            <button type="button" onclick="removeItem(this)" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemIndex++;
    attachEventListeners(row);
}

function removeItem(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
        calculateTotals();
    }
}

function attachEventListeners(row) {
    row.querySelector('.product-select').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const price = option.dataset.price || 0;
        row.querySelector('.price-input').value = price;
        calculateTotals();
    });
    row.querySelector('.quantity-input').addEventListener('input', calculateTotals);
    row.querySelector('.price-input').addEventListener('input', calculateTotals);
    row.querySelector('.tax-input').addEventListener('input', calculateTotals);
}

function calculateTotals() {
    let subtotal = 0;
    let taxTotal = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-input').value) || 0;
        
        const lineTotal = qty * price;
        const lineTax = lineTotal * (taxRate / 100);
        
        row.querySelector('.line-total').textContent = lineTotal.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        
        subtotal += lineTotal;
        taxTotal += lineTax;
    });

    document.getElementById('subtotal').textContent = subtotal.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('taxTotal').textContent = taxTotal.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('grandTotal').textContent = (subtotal + taxTotal).toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

document.querySelectorAll('.item-row').forEach(attachEventListeners);
</script>
@endsection
