@extends('layouts.dashboard')

@section('title', 'Bons de Commande')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bons de Commande Fournisseur</h1>
            <p class="mt-1 text-sm text-gray-500">Gérez vos commandes auprès des fournisseurs.</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouveau BC
        </a>
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
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="N° commande, fournisseur..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="w-40">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyé</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partiel</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Reçu</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="w-48">
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                <select name="supplier_id" id="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">Filtrer</button>
        </form>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livraison prévue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($purchaseOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('purchase-orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $order->order_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->supplier->company_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->order_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->expected_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $order->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'confirmed' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $order->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'received' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                @switch($order->status)
                                    @case('draft') Brouillon @break
                                    @case('sent') Envoyé @break
                                    @case('confirmed') Confirmé @break
                                    @case('partial') Partiel @break
                                    @case('received') Reçu @break
                                    @case('cancelled') Annulé @break
                                    @default {{ $order->status }}
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($order->total, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('purchase-orders.show', $order) }}" class="text-gray-600 hover:text-gray-900" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(in_array($order->status, ['draft', 'sent']))
                                    <a href="{{ route('purchase-orders.edit', $order) }}" class="text-primary-600 hover:text-primary-900" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2">Aucun bon de commande trouvé.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($purchaseOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
