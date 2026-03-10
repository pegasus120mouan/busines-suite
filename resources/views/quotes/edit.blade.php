@extends('layouts.dashboard')

@section('title', 'Modifier le devis')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('quotes.show', $quote) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au devis
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier le devis {{ $quote->quote_number }}</h1>
    </div>

    <form method="POST" action="{{ route('quotes.update', $quote) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                    <select name="customer_id" id="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="">Sélectionner un client</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $quote->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quote_date" class="block text-sm font-medium text-gray-700 mb-1">Date du devis *</label>
                    <input type="date" name="quote_date" id="quote_date" value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Valide jusqu'au *</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', $quote->valid_until->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Lignes du devis</h2>
                <button type="button" onclick="addItem()" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm bg-primary-50 text-primary-700 font-medium rounded-lg hover:bg-primary-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter une ligne
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-left text-xs font-medium text-gray-500 uppercase w-1/3">Description</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Qté</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Prix unit.</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-20">TVA %</th>
                            <th class="pb-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Total HT</th>
                            <th class="pb-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body"></tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Sous-total HT</span>
                        <span class="text-gray-900" id="subtotal">0 {{ $currencySymbol }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">TVA</span>
                        <span class="text-gray-900" id="tax-total">0 {{ $currencySymbol }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                        <span class="text-gray-900">Total TTC</span>
                        <span class="text-gray-900" id="grand-total">0 {{ $currencySymbol }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $quote->notes) }}</textarea>
                </div>
                <div>
                    <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">Conditions</label>
                    <textarea name="terms" id="terms" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('terms', $quote->terms) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('quotes.show', $quote) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Enregistrer</button>
        </div>
    </form>
</div>

<script>
const products = @json($products);
const existingItems = @json($quote->items);
const currencySymbol = '{{ $currencySymbol }}';
let itemIndex = 0;

function addItem(item = null) {
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    row.className = 'border-b border-gray-100 item-row';
    row.innerHTML = `
        <td class="py-3 pr-2">
            <select name="items[${itemIndex}][product_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="selectProduct(this)">
                <option value="">Produit personnalisé</option>
                ${products.map(p => `<option value="${p.id}" ${item?.product_id == p.id ? 'selected' : ''}>${p.name}</option>`).join('')}
            </select>
            <input type="text" name="items[${itemIndex}][description]" value="${item?.description || ''}" placeholder="Description *" class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
        </td>
        <td class="py-3 px-2">
            <input type="number" name="items[${itemIndex}][quantity]" value="${item?.quantity || 1}" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right item-qty" onchange="calculateRow(this)" required>
        </td>
        <td class="py-3 px-2">
            <input type="number" name="items[${itemIndex}][unit_price]" value="${item?.unit_price || 0}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right item-price" onchange="calculateRow(this)" required>
        </td>
        <td class="py-3 px-2">
            <input type="number" name="items[${itemIndex}][tax_rate]" value="${item?.tax_rate || 20}" min="0" max="100" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right item-tax" onchange="calculateRow(this)">
        </td>
        <td class="py-3 px-2 text-right">
            <span class="text-sm font-medium text-gray-900 item-total">0 ${currencySymbol}</span>
        </td>
        <td class="py-3 pl-2">
            <button type="button" onclick="removeItem(this)" class="p-1 text-gray-400 hover:text-red-600 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemIndex++;
    calculateRow(row.querySelector('.item-qty'));
}

function selectProduct(select) {
    const row = select.closest('tr');
    const product = products.find(p => p.id == select.value);
    if (product) {
        row.querySelector('input[name$="[description]"]').value = product.name;
        row.querySelector('input[name$="[unit_price]"]').value = product.selling_price;
        row.querySelector('input[name$="[tax_rate]"]').value = product.tax_rate || 20;
        calculateRow(row.querySelector('.item-qty'));
    }
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('tr').remove();
        calculateTotals();
    }
}

function calculateRow(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    row.querySelector('.item-total').textContent = formatPrice(qty * price) + ' ' + currencySymbol;
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0, taxTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;
        const lineTotal = qty * price;
        subtotal += lineTotal;
        taxTotal += lineTotal * (taxRate / 100);
    });
    document.getElementById('subtotal').textContent = formatPrice(subtotal) + ' ' + currencySymbol;
    document.getElementById('tax-total').textContent = formatPrice(taxTotal) + ' ' + currencySymbol;
    document.getElementById('grand-total').textContent = formatPrice(subtotal + taxTotal) + ' ' + currencySymbol;
}

function formatPrice(value) {
    return value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

document.addEventListener('DOMContentLoaded', function() {
    if (existingItems.length > 0) {
        existingItems.forEach(item => addItem(item));
    } else {
        addItem();
    }
});
</script>
@endsection
