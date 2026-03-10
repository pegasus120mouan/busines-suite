@extends('layouts.dashboard')

@section('title', 'Modifier le produit')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux produits
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier le produit</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $product->name }}</p>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Type -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Type</h2>
            <div class="grid grid-cols-2 gap-4">
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm hover:border-primary-500 has-[:checked]:border-primary-600 has-[:checked]:ring-2 has-[:checked]:ring-primary-600">
                    <input type="radio" name="type" value="product" class="sr-only" {{ old('type', $product->type) === 'product' ? 'checked' : '' }}>
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">Produit</span>
                            <span class="mt-1 text-sm text-gray-500">Article physique avec stock</span>
                        </span>
                    </span>
                </label>
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm hover:border-primary-500 has-[:checked]:border-primary-600 has-[:checked]:ring-2 has-[:checked]:ring-primary-600">
                    <input type="radio" name="type" value="service" class="sr-only" {{ old('type', $product->type) === 'service' ? 'checked' : '' }}>
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">Service</span>
                            <span class="mt-1 text-sm text-gray-500">Prestation sans stock</span>
                        </span>
                    </span>
                </label>
            </div>
        </div>

        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU / Référence</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Code-barres</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Aucune catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                    <select name="supplier_id" id="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Aucun fournisseur</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tarification</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat HT</label>
                    <div class="relative">
                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01" min="0" class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <span class="absolute right-3 top-2.5 text-gray-500">{{ $currencySymbol }}</span>
                    </div>
                </div>
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">Prix de vente HT *</label>
                    <div class="relative">
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0" class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <span class="absolute right-3 top-2.5 text-gray-500">{{ $currencySymbol }}</span>
                    </div>
                </div>
                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">Taux de TVA</label>
                    <div class="relative">
                        <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $product->tax_rate) }}" step="0.1" min="0" max="100" class="w-full px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <span class="absolute right-3 top-2.5 text-gray-500">%</span>
                    </div>
                </div>
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                    <select name="unit" id="unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="unit" {{ old('unit', $product->unit) === 'unit' ? 'selected' : '' }}>Unité</option>
                        <option value="hour" {{ old('unit', $product->unit) === 'hour' ? 'selected' : '' }}>Heure</option>
                        <option value="day" {{ old('unit', $product->unit) === 'day' ? 'selected' : '' }}>Jour</option>
                        <option value="kg" {{ old('unit', $product->unit) === 'kg' ? 'selected' : '' }}>Kilogramme</option>
                        <option value="m" {{ old('unit', $product->unit) === 'm' ? 'selected' : '' }}>Mètre</option>
                        <option value="m2" {{ old('unit', $product->unit) === 'm2' ? 'selected' : '' }}>Mètre carré</option>
                        <option value="l" {{ old('unit', $product->unit) === 'l' ? 'selected' : '' }}>Litre</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Stock -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Gestion du stock</h2>
                <label class="flex items-center">
                    <input type="checkbox" name="track_stock" value="1" {{ old('track_stock', $product->track_stock) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Suivre le stock</span>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_stock_level" class="block text-sm font-medium text-gray-700 mb-1">Stock minimum</label>
                    <input type="number" name="min_stock_level" id="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="reorder_point" class="block text-sm font-medium text-gray-700 mb-1">Point de réapprovisionnement</label>
                    <input type="number" name="reorder_point" id="reorder_point" value="{{ old('reorder_point', $product->reorder_point) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="ml-2 text-sm font-medium text-gray-700">Produit actif</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('products.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
