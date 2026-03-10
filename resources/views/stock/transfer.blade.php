@extends('layouts.dashboard')

@section('title', 'Transfert de Stock')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('stock.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transfert de Stock</h1>
            <p class="mt-1 text-sm text-gray-500">Transférer du stock entre entrepôts.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('stock.transfer') }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produit *</label>
                <select name="product_id" id="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
                @error('product_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="from_warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Entrepôt source *</label>
                <select name="from_warehouse_id" id="from_warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Sélectionner</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('from_warehouse_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="to_warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Entrepôt destination *</label>
                <select name="to_warehouse_id" id="to_warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Sélectionner</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('to_warehouse_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité à transférer *</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" step="0.01" min="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                @error('quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                <textarea name="reason" id="reason" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Raison du transfert...">{{ old('reason') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
            <a href="{{ route('stock.index') }}" class="px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">Transférer</button>
        </div>
    </form>
</div>
@endsection
