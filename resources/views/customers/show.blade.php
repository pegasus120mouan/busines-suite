@extends('layouts.dashboard')

@section('title', $customer->display_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux clients
            </a>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-primary-700">{{ substr($customer->display_name, 0, 2) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->display_name }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->type === 'company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $customer->type === 'company' ? 'Entreprise' : 'Particulier' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $customer->status === 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('invoices.create', ['customer' => $customer->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Nouvelle facture
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de contact</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($customer->type === 'company' && $customer->first_name)
                        <div>
                            <p class="text-sm text-gray-500">Contact</p>
                            <p class="text-sm font-medium text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <a href="mailto:{{ $customer->email }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">{{ $customer->email }}</a>
                    </div>
                    @if($customer->phone)
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <a href="tel:{{ $customer->phone }}" class="text-sm font-medium text-gray-900">{{ $customer->phone }}</a>
                        </div>
                    @endif
                    @if($customer->mobile)
                        <div>
                            <p class="text-sm text-gray-500">Mobile</p>
                            <a href="tel:{{ $customer->mobile }}" class="text-sm font-medium text-gray-900">{{ $customer->mobile }}</a>
                        </div>
                    @endif
                    @if($customer->website)
                        <div>
                            <p class="text-sm text-gray-500">Site web</p>
                            <a href="{{ $customer->website }}" target="_blank" class="text-sm font-medium text-primary-600 hover:text-primary-500">{{ $customer->website }}</a>
                        </div>
                    @endif
                    @if($customer->tax_number)
                        <div>
                            <p class="text-sm text-gray-500">N° TVA / SIRET</p>
                            <p class="text-sm font-medium text-gray-900">{{ $customer->tax_number }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Address -->
            @if($customer->billing_address || $customer->billing_city)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse de facturation</h2>
                    <address class="not-italic text-sm text-gray-700">
                        @if($customer->billing_address)
                            {{ $customer->billing_address }}<br>
                        @endif
                        @if($customer->billing_postal_code || $customer->billing_city)
                            {{ $customer->billing_postal_code }} {{ $customer->billing_city }}<br>
                        @endif
                        @if($customer->billing_country)
                            {{ $customer->billing_country }}
                        @endif
                    </address>
                </div>
            @endif

            <!-- Recent Invoices -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Dernières factures</h2>
                    <a href="{{ route('invoices.index', ['customer' => $customer->id]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">Voir tout</a>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($customer->invoices as $invoice)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                <p class="text-xs text-gray-500">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($invoice->status === 'paid') bg-green-100 text-green-800
                                    @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                                    @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            <p>Aucune facture</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Total facturé</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($customer->invoices->sum('total'), 0, ',', ' ') }} {{ $currencySymbol }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Factures</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $customer->invoices->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Devis</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $customer->quotes->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Client depuis</p>
                        <p class="text-sm font-medium text-gray-900">{{ $customer->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($customer->notes)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $customer->notes }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h2>
                <div class="space-y-2">
                    <a href="{{ route('quotes.create', ['customer' => $customer->id]) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Créer un devis
                    </a>
                    <a href="{{ route('invoices.create', ['customer' => $customer->id]) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Créer une facture
                    </a>
                    <a href="mailto:{{ $customer->email }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Envoyer un email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
