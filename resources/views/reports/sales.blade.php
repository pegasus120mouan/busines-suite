@extends('layouts.dashboard')

@section('title', 'Rapport des Ventes')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('reports.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Rapport des Ventes</h1>
                <p class="mt-1 text-sm text-gray-500">Du {{ $startDate->format('d/m/Y') }} au {{ $endDate->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">Filtrer</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Facturé</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($summary['total_invoiced'], 0, ',', ' ') }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ $summary['invoice_count'] }} factures</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Encaissé</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($summary['total_paid'], 0, ',', ' ') }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ $summary['paid_count'] }} factures payées</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Reste à Encaisser</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ number_format($summary['total_outstanding'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">En Retard</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ $summary['overdue_count'] }}</p>
            <p class="mt-1 text-sm text-gray-500">factures en retard</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Sales Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ventes Mensuelles</h2>
            <div class="space-y-3">
                @foreach($salesByMonth as $month)
                    @php
                        $maxTotal = $salesByMonth->max('total') ?: 1;
                        $percentage = ($month->total / $maxTotal) * 100;
                        $monthName = \Carbon\Carbon::create($month->year, $month->month)->translatedFormat('M Y');
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $monthName }}</span>
                            <span class="font-medium">{{ number_format($month->total, 0, ',', ' ') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Clients</h2>
            <div class="space-y-3">
                @forelse($topCustomers as $customer)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-primary-700 font-medium text-xs">{{ strtoupper(substr($customer->display_name, 0, 2)) }}</span>
                            </div>
                            <span class="text-sm text-gray-900">{{ $customer->display_name }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($customer->total_sales, 0, ',', ' ') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune donnée disponible.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Détail des Factures</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facture</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Payé</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $invoice->invoice_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $invoice->customer->display_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $invoice->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            ">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($invoice->total, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">{{ number_format($invoice->amount_paid, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucune facture pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
