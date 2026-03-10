@extends('layouts.dashboard')

@section('title', 'Facture ' . $invoice->invoice_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux factures
            </a>
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h1>
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'sent' => 'bg-blue-100 text-blue-800',
                        'paid' => 'bg-green-100 text-green-800',
                        'partial' => 'bg-yellow-100 text-yellow-800',
                        'overdue' => 'bg-red-100 text-red-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                    ];
                    $statusLabels = [
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'partial' => 'Partielle',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                </span>
            </div>
        </div>
        <div class="flex gap-3">
            @if($invoice->status === 'draft')
                <form method="POST" action="{{ route('invoices.send', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Marquer envoyée
                    </button>
                </form>
                <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            @endif
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Details -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Client</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $invoice->customer?->display_name }}</p>
                        @if($invoice->customer?->email)
                            <p class="text-sm text-gray-600">{{ $invoice->customer->email }}</p>
                        @endif
                        @if($invoice->customer?->billing_address)
                            <p class="text-sm text-gray-600 mt-2">
                                {{ $invoice->customer->billing_address }}<br>
                                {{ $invoice->customer->billing_postal_code }} {{ $invoice->customer->billing_city }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Date de facture</p>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date d'échéance</p>
                            <p class="text-sm font-medium {{ $invoice->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">{{ $invoice->due_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
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
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $item->description }}</p>
                                        @if($item->product)
                                            <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                                        @endif
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

                <!-- Totals -->
                <div class="mt-4 flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sous-total HT</span>
                            <span class="text-gray-900">{{ number_format($invoice->subtotal, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">TVA</span>
                            <span class="text-gray-900">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Remise</span>
                                <span class="text-red-600">-{{ number_format($invoice->discount_amount, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span class="text-gray-900">Total TTC</span>
                            <span class="text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                        @if($invoice->balance_due > 0 && $invoice->balance_due < $invoice->total)
                            <div class="flex justify-between text-sm pt-2">
                                <span class="text-gray-500">Déjà payé</span>
                                <span class="text-green-600">{{ number_format($invoice->total - $invoice->balance_due, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-red-600">
                                <span>Reste à payer</span>
                                <span>{{ number_format($invoice->balance_due, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes || $invoice->terms)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    @if($invoice->notes)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Notes</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Conditions</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $invoice->terms }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Paiements</h3>
                @if($invoice->payments->count() > 0)
                    <div class="space-y-3 mb-4">
                        @foreach($invoice->payments as $payment)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->payment_date->format('d/m/Y') }} - {{ ucfirst($payment->method) }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Payé
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-4">Aucun paiement enregistré</p>
                @endif

                @if($invoice->balance_due > 0 && !in_array($invoice->status, ['draft', 'cancelled']))
                    <button type="button" onclick="document.getElementById('payment-modal').classList.remove('hidden')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Enregistrer un paiement
                    </button>
                @endif
            </div>

            <!-- Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créée par</span>
                        <span class="text-gray-900">{{ $invoice->user?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créée le</span>
                        <span class="text-gray-900">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($invoice->sent_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Envoyée le</span>
                            <span class="text-gray-900">{{ $invoice->sent_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($invoice->paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Payée le</span>
                            <span class="text-gray-900">{{ $invoice->paid_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('payment-modal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Enregistrer un paiement</h3>
            <form method="POST" action="{{ route('invoices.payment', $invoice) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant *</label>
                        <div class="relative">
                            <input type="number" name="amount" value="{{ $invoice->balance_due }}" step="0.01" min="0.01" max="{{ $invoice->balance_due }}" class="w-full px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <span class="absolute right-3 top-2.5 text-gray-500">{{ $currencySymbol }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Méthode *</label>
                        <select name="method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="bank_transfer">Virement bancaire</option>
                            <option value="check">Chèque</option>
                            <option value="cash">Espèces</option>
                            <option value="credit_card">Carte bancaire</option>
                            <option value="paypal">PayPal</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                        <input type="text" name="reference" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="document.getElementById('payment-modal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
