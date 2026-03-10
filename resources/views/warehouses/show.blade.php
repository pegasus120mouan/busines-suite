@extends('layouts.dashboard')

@section('title', $warehouse->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('warehouses.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $warehouse->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $warehouse->code }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('warehouses.edit', $warehouse) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Warehouse Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $warehouse->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $warehouse->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        @if($warehouse->is_default)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-1">Par défaut</span>
                        @endif
                    </dd>
                </div>
                @if($warehouse->address)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $warehouse->address }}<br>
                            @if($warehouse->postal_code || $warehouse->city)
                                {{ $warehouse->postal_code }} {{ $warehouse->city }}
                            @endif
                        </dd>
                    </div>
                @endif
                @if($warehouse->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $warehouse->phone }}</dd>
                    </div>
                @endif
                @if($warehouse->email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $warehouse->email }}</dd>
                    </div>
                @endif
                @if($warehouse->manager_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Responsable</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $warehouse->manager_name }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Stats -->
        <div class="lg:col-span-2 grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500">Produits en stock</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stocks->total() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500">Valeur totale</p>
                <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($totalValue, 0, ',', ' ') }}</p>
            </div>
        </div>
    </div>

    <!-- Stock List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Stock dans cet entrepôt</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valeur</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stocks as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('products.show', $stock->product) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $stock->product->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stock->product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ $stock->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($stock->product->sale_price, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($stock->quantity * $stock->product->sale_price, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucun stock dans cet entrepôt.</td>
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
