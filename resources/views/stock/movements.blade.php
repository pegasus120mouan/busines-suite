@extends('layouts.dashboard')

@section('title', 'Mouvements de Stock')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('stock.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mouvements de Stock</h1>
                <p class="mt-1 text-sm text-gray-500">Historique des entrées, sorties et transferts.</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-40">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrée</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Sortie</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustement</option>
                    <option value="transfer_in" {{ request('type') == 'transfer_in' ? 'selected' : '' }}>Transfert entrant</option>
                    <option value="transfer_out" {{ request('type') == 'transfer_out' ? 'selected' : '' }}>Transfert sortant</option>
                </select>
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
            <div class="w-48">
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                <select name="product_id" id="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">Filtrer</button>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrepôt</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avant</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Après</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $movement->type === 'in' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $movement->type === 'out' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $movement->type === 'adjustment' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $movement->type === 'transfer_in' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $movement->type === 'transfer_out' ? 'bg-orange-100 text-orange-800' : '' }}
                            ">
                                @switch($movement->type)
                                    @case('in') Entrée @break
                                    @case('out') Sortie @break
                                    @case('adjustment') Ajustement @break
                                    @case('transfer_in') Transfert + @break
                                    @case('transfer_out') Transfert - @break
                                    @default {{ $movement->type }}
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('products.show', $movement->product) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $movement->product->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movement->warehouse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ in_array($movement->type, ['in', 'transfer_in']) ? 'text-green-600' : 'text-red-600' }}">
                            {{ in_array($movement->type, ['in', 'transfer_in']) ? '+' : '-' }}{{ number_format($movement->quantity, 0, ',', ' ') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ number_format($movement->quantity_before, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($movement->quantity_after, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">Aucun mouvement trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($movements->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
