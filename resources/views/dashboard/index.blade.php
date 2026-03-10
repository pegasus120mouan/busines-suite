@extends('layouts.dashboard')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-1 text-sm text-gray-500">Voici un aperçu de votre activité ce mois-ci.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter
            </a>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 rounded-lg text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle facture
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    +12%
                </span>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['revenue'], 0, ',', ' ') }} {{ $currencySymbol }}</p>
            </div>
        </div>

        <!-- Expenses -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    -5%
                </span>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Dépenses</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['expenses'], 0, ',', ' ') }} {{ $currencySymbol }}</p>
            </div>
        </div>

        <!-- Profit -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Bénéfice net</p>
                <p class="text-2xl font-bold {{ $stats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($stats['profit'], 0, ',', ' ') }} {{ $currencySymbol }}
                </p>
            </div>
        </div>

        <!-- Pending Invoices -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Factures en attente</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_invoices'], 0, ',', ' ') }} {{ $currencySymbol }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Clients actifs</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['customers_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Produits</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['products_count'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Devis en attente</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['quotes_pending'] }}</p>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue & Expenses Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Évolution CA & Dépenses</h3>
                <span class="text-sm text-gray-500">12 derniers mois</span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Clients</h3>
            <div class="space-y-4">
                @forelse($topCustomers as $customer)
                    @if($customer->total_revenue > 0)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-semibold text-primary-700">{{ strtoupper(substr($customer->display_name, 0, 2)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 truncate max-w-[120px]">{{ $customer->display_name }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($customer->total_revenue, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                        </div>
                    @endif
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Aucune donnée</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Invoice Status Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut des factures</h3>
            <div class="h-48">
                <canvas id="invoiceStatusChart"></canvas>
            </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Comparaison mensuelle</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $currentMonth = end($monthlyRevenue);
                    $previousMonth = $monthlyRevenue[count($monthlyRevenue) - 2] ?? ['value' => 0];
                    $revenueChange = $previousMonth['value'] > 0 ? (($currentMonth['value'] - $previousMonth['value']) / $previousMonth['value']) * 100 : 0;
                    
                    $currentExpense = end($monthlyExpenses);
                    $previousExpense = $monthlyExpenses[count($monthlyExpenses) - 2] ?? ['value' => 0];
                    $expenseChange = $previousExpense['value'] > 0 ? (($currentExpense['value'] - $previousExpense['value']) / $previousExpense['value']) * 100 : 0;
                @endphp
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">CA ce mois</p>
                    <p class="text-lg font-bold text-green-600">{{ number_format($currentMonth['value'], 0, ',', ' ') }}</p>
                    <p class="text-xs {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $revenueChange >= 0 ? '+' : '' }}{{ number_format($revenueChange, 1) }}%
                    </p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Dépenses ce mois</p>
                    <p class="text-lg font-bold text-red-600">{{ number_format($currentExpense['value'], 0, ',', ' ') }}</p>
                    <p class="text-xs {{ $expenseChange <= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $expenseChange >= 0 ? '+' : '' }}{{ number_format($expenseChange, 1) }}%
                    </p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Marge ce mois</p>
                    <p class="text-lg font-bold text-blue-600">{{ number_format($currentMonth['value'] - $currentExpense['value'], 0, ',', ' ') }}</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">CA annuel</p>
                    <p class="text-lg font-bold text-purple-600">{{ number_format(array_sum(array_column($monthlyRevenue, 'value')), 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Invoices -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Dernières factures</h3>
                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500">Voir tout</a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentInvoices as $invoice)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                <p class="text-xs text-gray-500">{{ $invoice->customer?->display_name ?? 'N/A' }}</p>
                            </div>
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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2">Aucune facture récente</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Factures en retard</h3>
                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500">Voir tout</a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($overdueInvoices as $invoice)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                <p class="text-xs text-gray-500">{{ $invoice->customer?->display_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-red-600">{{ number_format($invoice->balance_due, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                            <p class="text-xs text-gray-500">Échéance: {{ $invoice->due_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-green-600">Aucune facture en retard 🎉</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue & Expenses Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyRevenue, 'short')) !!},
                datasets: [
                    {
                        label: 'Chiffre d\'affaires',
                        data: {!! json_encode(array_column($monthlyRevenue, 'value')) !!},
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Dépenses',
                        data: {!! json_encode(array_column($monthlyExpenses, 'value')) !!},
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR');
                            }
                        }
                    }
                }
            }
        });
    }

    // Invoice Status Chart
    const statusCtx = document.getElementById('invoiceStatusChart');
    if (statusCtx) {
        const statusData = @json($invoicesByStatus);
        const statusLabels = {
            'draft': 'Brouillon',
            'sent': 'Envoyée',
            'paid': 'Payée',
            'partial': 'Partielle',
            'overdue': 'En retard',
            'cancelled': 'Annulée'
        };
        const statusColors = {
            'draft': '#9CA3AF',
            'sent': '#3B82F6',
            'paid': '#10B981',
            'partial': '#F59E0B',
            'overdue': '#EF4444',
            'cancelled': '#6B7280'
        };
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(k => statusLabels[k] || k),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: Object.keys(statusData).map(k => statusColors[k] || '#9CA3AF'),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 8,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
