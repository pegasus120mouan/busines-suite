@extends('layouts.dashboard')

@section('title', 'Devis ' . $quote->quote_number)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('quotes.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux devis
            </a>
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ $quote->quote_number }}</h1>
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'sent' => 'bg-blue-100 text-blue-800',
                        'accepted' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'converted' => 'bg-purple-100 text-purple-800',
                    ];
                    $statusLabels = [
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyé',
                        'accepted' => 'Accepté',
                        'rejected' => 'Refusé',
                        'converted' => 'Converti',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$quote->status] ?? $quote->status }}
                </span>
            </div>
        </div>
        <div class="flex gap-3">
            @if($quote->status === 'draft')
                <form method="POST" action="{{ route('quotes.send', $quote) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Marquer envoyé
                    </button>
                </form>
            @endif
            @if($quote->status === 'sent')
                <form method="POST" action="{{ route('quotes.accept', $quote) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Accepter
                    </button>
                </form>
            @endif
            @if($quote->status === 'accepted' && !$quote->invoice_id)
                <form method="POST" action="{{ route('quotes.convert', $quote) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Convertir en facture
                    </button>
                </form>
            @endif
            @if(in_array($quote->status, ['draft', 'sent']))
                <a href="{{ route('quotes.edit', $quote) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            @endif
            <a href="{{ route('quotes.pdf', $quote) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>
        </div>
    </div>

    @if($quote->invoice)
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-purple-800">
                    Ce devis a été converti en facture 
                    <a href="{{ route('invoices.show', $quote->invoice) }}" class="font-medium underline">{{ $quote->invoice->invoice_number }}</a>
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Client</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $quote->customer?->display_name }}</p>
                        @if($quote->customer?->email)
                            <p class="text-sm text-gray-600">{{ $quote->customer->email }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Date du devis</p>
                            <p class="text-sm font-medium text-gray-900">{{ $quote->quote_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Valide jusqu'au</p>
                            <p class="text-sm font-medium {{ $quote->valid_until->isPast() ? 'text-red-600' : 'text-gray-900' }}">{{ $quote->valid_until->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qté</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">TVA</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total HT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($quote->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $item->description }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($item->unit_price, 0, ',', ' ') }} {{ $currencySymbol }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-500">{{ $item->tax_rate }}%</td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ number_format($item->subtotal, 0, ',', ' ') }} {{ $currencySymbol }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sous-total HT</span>
                            <span class="text-gray-900">{{ number_format($quote->subtotal, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">TVA</span>
                            <span class="text-gray-900">{{ number_format($quote->tax_amount, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span class="text-gray-900">Total TTC</span>
                            <span class="text-gray-900">{{ number_format($quote->total, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($quote->notes || $quote->terms)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    @if($quote->notes)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Notes</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $quote->notes }}</p>
                        </div>
                    @endif
                    @if($quote->terms)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Conditions</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $quote->terms }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé par</span>
                        <span class="text-gray-900">{{ $quote->user?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé le</span>
                        <span class="text-gray-900">{{ $quote->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($quote->sent_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Envoyé le</span>
                            <span class="text-gray-900">{{ $quote->sent_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($quote->accepted_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Accepté le</span>
                            <span class="text-gray-900">{{ $quote->accepted_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
