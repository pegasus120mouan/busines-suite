@extends('layouts.dashboard')

@section('title', 'État des Stocks')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">État des Stocks</h1>
            <p class="mt-1 text-sm text-gray-500">Inventaire et valeur des stocks.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Produits Suivis</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_products'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Valeur Totale Stock</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($summary['total_stock_value'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Stock Bas</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $summary['low_stock_count'] }}</p>
            <p class="mt-1 text-sm text-gray-500">produits à réapprovisionner</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Rupture de Stock</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ $summary['out_of_stock_count'] }}</p>
            <p class="mt-1 text-sm text-gray-500">produits épuisés</p>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
    <!-- Low Stock Alert -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h2 class="text-lg font-semibold text-yellow-800">Alertes Stock Bas</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lowStockProducts->take(6) as $product)
                <div class="bg-white rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold {{ $product->total_stock <= 0 ? 'text-red-600' : 'text-yellow-600' }}">{{ $product->total_stock }}</p>
                            <p class="text-xs text-gray-500">Min: {{ $product->min_stock_level }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Inventaire Complet</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix Vente</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valeur</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products->sortByDesc('stock_value') as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('products.show', $product) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $product->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ $product->total_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ $product->min_stock_level }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($product->sale_price, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($product->stock_value, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($product->total_stock <= 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rupture</span>
                            @elseif($product->total_stock <= $product->min_stock_level)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Stock bas</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">Aucun produit avec suivi de stock.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
