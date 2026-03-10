@extends('layouts.dashboard')

@section('title', 'Compte de Résultat')

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
                <h1 class="text-2xl font-bold text-gray-900">Compte de Résultat</h1>
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Revenus</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($revenue, 0, ',', ' ') }}</p>
            <p class="mt-1 text-sm text-gray-500">Factures payées</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Dépenses</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($expenses, 0, ',', ' ') }}</p>
            <p class="mt-1 text-sm text-gray-500">Dépenses approuvées</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Bénéfice Net</p>
            <p class="mt-2 text-3xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 0, ',', ' ') }}</p>
            <p class="mt-1 text-sm text-gray-500">Revenus - Dépenses</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Marge Bénéficiaire</p>
            <p class="mt-2 text-3xl font-bold {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($margin, 1) }}%</p>
            <p class="mt-1 text-sm text-gray-500">Bénéfice / Revenus</p>
        </div>
    </div>

    <!-- Visual Summary -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Résumé Visuel</h2>
        <div class="flex items-center gap-8">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Revenus</span>
                    <span class="text-sm font-bold text-green-600">{{ number_format($revenue, 0, ',', ' ') }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-500 h-4 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-400">-</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Dépenses</span>
                    <span class="text-sm font-bold text-red-600">{{ number_format($expenses, 0, ',', ' ') }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    @php $expensePercentage = $revenue > 0 ? min(($expenses / $revenue) * 100, 100) : 0; @endphp
                    <div class="bg-red-500 h-4 rounded-full" style="width: {{ $expensePercentage }}%"></div>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-400">=</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Bénéfice</span>
                    <span class="text-sm font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 0, ',', ' ') }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    @php $profitPercentage = $revenue > 0 ? max(($profit / $revenue) * 100, 0) : 0; @endphp
                    <div class="{{ $profit >= 0 ? 'bg-blue-500' : 'bg-red-500' }} h-4 rounded-full" style="width: {{ abs($profitPercentage) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Évolution Mensuelle</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mois</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Dépenses</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Bénéfice</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Marge</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($monthlyData as $data)
                    @php $monthMargin = $data['revenue'] > 0 ? ($data['profit'] / $data['revenue']) * 100 : 0; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['month'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">{{ number_format($data['revenue'], 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">{{ number_format($data['expenses'], 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $data['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($data['profit'], 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $monthMargin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($monthMargin, 1) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucune donnée disponible.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">{{ number_format($revenue, 0, ',', ' ') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-600">{{ number_format($expenses, 0, ',', ' ') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 0, ',', ' ') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($margin, 1) }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
