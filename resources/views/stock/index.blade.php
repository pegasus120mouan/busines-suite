@extends('layouts.dashboard')

@section('title', 'Gestion des Stocks')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Stocks</h1>
            <p class="mt-1 text-sm text-gray-500">Suivez les niveaux de stock par entrepôt.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('stock.adjust.form') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Ajuster le stock
            </a>
            <a href="{{ route('stock.transfer.form') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Transférer
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, SKU..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="w-48">
                <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Entrepôt</label>
                <select name="warehouse_id" id="warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="text-sm text-gray-700">Stock bas uniquement</span>
                </label>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">Filtrer</button>
            <a href="{{ route('stock.movements') }}" class="px-4 py-2 text-primary-600 hover:text-primary-800">Voir les mouvements</a>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrepôt</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stocks as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('products.show', $stock->product) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $stock->product->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stock->product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($stock->quantity, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ $stock->min_quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($stock->quantity <= 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rupture</span>
                            @elseif($stock->quantity <= $stock->min_quantity)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Stock bas</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucun stock trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($stocks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
