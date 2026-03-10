@extends('layouts.dashboard')

@section('title', 'Créances Clients')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('reports.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Créances Clients</h1>
            <p class="mt-1 text-sm text-gray-500">Suivi des factures impayées et échéances.</p>
        </div>
    </div>

    <!-- Aging Summary -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Total Dû</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($summary['total_outstanding'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">À échoir</p>
            <p class="mt-1 text-2xl font-bold text-green-600">{{ number_format($summary['current'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">1-30 jours</p>
            <p class="mt-1 text-2xl font-bold text-yellow-600">{{ number_format($summary['overdue_1_30'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">31-60 jours</p>
            <p class="mt-1 text-2xl font-bold text-orange-600">{{ number_format($summary['overdue_31_60'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">61-90 jours</p>
            <p class="mt-1 text-2xl font-bold text-red-500">{{ number_format($summary['overdue_61_90'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">+90 jours</p>
            <p class="mt-1 text-2xl font-bold text-red-700">{{ number_format($summary['overdue_90_plus'], 0, ',', ' ') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- By Customer -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Par Client</h2>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($byCustomer as $customer)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-primary-700 font-medium text-xs">{{ strtoupper(substr($customer->display_name, 0, 2)) }}</span>
                            </div>
                            <span class="text-sm text-gray-900">{{ Str::limit($customer->display_name, 20) }}</span>
                        </div>
                        <span class="text-sm font-medium text-red-600">{{ number_format($customer->total_due, 0, ',', ' ') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune créance.</p>
                @endforelse
            </div>
        </div>

        <!-- Invoices List -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Factures Impayées</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facture</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Retard</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Dû</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoices as $invoice)
                            @php
                                $daysOverdue = $invoice->due_date->isPast() ? $invoice->due_date->diffInDays(now()) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $invoice->invoice_number }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ Str::limit($invoice->customer->display_name ?? '-', 20) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($daysOverdue > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $daysOverdue <= 30 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $daysOverdue > 30 && $daysOverdue <= 60 ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $daysOverdue > 60 ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ $daysOverdue }} jours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">À échoir</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">{{ number_format($invoice->balance_due, 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucune facture impayée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
