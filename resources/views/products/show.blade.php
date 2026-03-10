@extends('layouts.dashboard')

@section('title', $product->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux produits
            </a>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center">
                    @if($product->type === 'service')
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->type === 'service' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $product->type === 'service' ? 'Service' : 'Produit' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        @if($product->sku)
                            <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Modifier
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Détails</h2>
                <div class="grid grid-cols-2 gap-4">
                    @if($product->category)
                        <div>
                            <p class="text-sm text-gray-500">Catégorie</p>
                            <p class="text-sm font-medium text-gray-900">{{ $product->category->name }}</p>
                        </div>
                    @endif
                    @if($product->supplier)
                        <div>
                            <p class="text-sm text-gray-500">Fournisseur</p>
                            <p class="text-sm font-medium text-gray-900">{{ $product->supplier->company_name }}</p>
                        </div>
                    @endif
                    @if($product->barcode)
                        <div>
                            <p class="text-sm text-gray-500">Code-barres</p>
                            <p class="text-sm font-medium text-gray-900">{{ $product->barcode }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Unité</p>
                        <p class="text-sm font-medium text-gray-900">{{ $product->unit ?? 'Unité' }}</p>
                    </div>
                </div>
                @if($product->description)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-1">Description</p>
                        <p class="text-sm text-gray-700">{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Stock by Warehouse -->
            @if($product->track_stock && $product->stocks->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Stock par entrepôt</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($product->stocks as $stock)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $stock->warehouse->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $stock->warehouse->city ?? '' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold {{ $stock->quantity <= $product->min_stock_level ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $stock->quantity }} {{ $product->unit ?? 'unités' }}
                                    </p>
                                    @if($stock->reserved_quantity > 0)
                                        <p class="text-xs text-gray-500">{{ $stock->reserved_quantity }} réservés</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Pricing -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tarification</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Prix de vente HT</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($product->selling_price, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                    </div>
                    @if($product->purchase_price)
                        <div>
                            <p class="text-sm text-gray-500">Prix d'achat HT</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($product->purchase_price, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Marge</p>
                            <p class="text-lg font-semibold text-green-600">{{ number_format($product->profit_margin, 1) }}%</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">TVA</p>
                        <p class="text-sm font-medium text-gray-900">{{ $product->tax_rate ?? 20 }}%</p>
                    </div>
                </div>
            </div>

            <!-- Stock Info -->
            @if($product->track_stock)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Stock total</p>
                            @php $totalStock = $product->total_stock; @endphp
                            <p class="text-2xl font-bold {{ $totalStock <= $product->min_stock_level ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $totalStock }} {{ $product->unit ?? 'unités' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Stock minimum</p>
                            <p class="text-sm font-medium text-gray-900">{{ $product->min_stock_level ?? 0 }}</p>
                        </div>
                        @if($product->reorder_point)
                            <div>
                                <p class="text-sm text-gray-500">Point de réappro.</p>
                                <p class="text-sm font-medium text-gray-900">{{ $product->reorder_point }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Metadata -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé le</span>
                        <span class="text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Modifié le</span>
                        <span class="text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
