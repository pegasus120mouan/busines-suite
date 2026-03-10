@extends('layouts.dashboard')

@section('title', 'BC ' . $purchaseOrder->order_number)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('purchase-orders.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $purchaseOrder->order_number }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $purchaseOrder->supplier->company_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($purchaseOrder->status === 'draft')
                <form method="POST" action="{{ route('purchase-orders.send', $purchaseOrder) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer
                    </button>
                </form>
            @endif
            @if($purchaseOrder->status === 'sent')
                <form method="POST" action="{{ route('purchase-orders.confirm', $purchaseOrder) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmer
                    </button>
                </form>
            @endif
            @if(in_array($purchaseOrder->status, ['draft', 'sent']))
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            @endif
            @if(!in_array($purchaseOrder->status, ['received', 'cancelled']))
                <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" class="inline" onsubmit="return confirm('Annuler ce bon de commande ?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 font-medium rounded-lg hover:bg-red-100">
                        Annuler
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $purchaseOrder->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $purchaseOrder->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $purchaseOrder->status === 'confirmed' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $purchaseOrder->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $purchaseOrder->status === 'received' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $purchaseOrder->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                @switch($purchaseOrder->status)
                                    @case('draft') Brouillon @break
                                    @case('sent') Envoyé @break
                                    @case('confirmed') Confirmé @break
                                    @case('partial') Partiel @break
                                    @case('received') Reçu @break
                                    @case('cancelled') Annulé @break
                                @endswitch
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de commande</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->order_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Livraison prévue</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->expected_date?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Entrepôt</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->warehouse->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Créé par</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->user->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Articles</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qté commandée</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qté reçue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix unit.</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total HT</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->product->name ?? $item->description }}</div>
                                    @if($item->product)
                                        <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm">{{ number_format($item->quantity, 0, ',', ' ') }}</td>
                                <td class="px-6 py-4 text-right text-sm {{ $item->received_quantity >= $item->quantity ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ number_format($item->received_quantity, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">{{ number_format($item->subtotal, 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Sous-total HT</td>
                            <td class="px-6 py-3 text-right text-sm font-medium">{{ number_format($purchaseOrder->subtotal, 0, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">TVA</td>
                            <td class="px-6 py-3 text-right text-sm font-medium">{{ number_format($purchaseOrder->tax_amount, 0, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-lg font-bold">Total TTC</td>
                            <td class="px-6 py-3 text-right text-lg font-bold text-primary-600">{{ number_format($purchaseOrder->total, 0, ',', ' ') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($purchaseOrder->notes)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Notes</h2>
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Supplier -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Fournisseur</h2>
                <div class="space-y-2">
                    <p class="font-medium text-gray-900">{{ $purchaseOrder->supplier->company_name }}</p>
                    @if($purchaseOrder->supplier->contact_name)
                        <p class="text-sm text-gray-600">{{ $purchaseOrder->supplier->contact_name }}</p>
                    @endif
                    @if($purchaseOrder->supplier->email)
                        <p class="text-sm text-gray-600">{{ $purchaseOrder->supplier->email }}</p>
                    @endif
                    @if($purchaseOrder->supplier->phone)
                        <p class="text-sm text-gray-600">{{ $purchaseOrder->supplier->phone }}</p>
                    @endif
                </div>
            </div>

            <!-- Reception Form -->
            @if(in_array($purchaseOrder->status, ['sent', 'confirmed', 'partial']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Réception</h2>
                    <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}">
                        @csrf
                        <div class="space-y-3">
                            @foreach($purchaseOrder->items as $item)
                                @if($item->received_quantity < $item->quantity)
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name ?? $item->description }}</p>
                                            <p class="text-xs text-gray-500">Reste: {{ $item->quantity - $item->received_quantity }}</p>
                                        </div>
                                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                        <input type="number" name="items[{{ $loop->index }}][received_quantity]" value="0" min="0" max="{{ $item->quantity - $item->received_quantity }}" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-right">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <button type="submit" class="mt-4 w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
                            Enregistrer la réception
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
