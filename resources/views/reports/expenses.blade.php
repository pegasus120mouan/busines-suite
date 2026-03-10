@extends('layouts.dashboard')

@section('title', 'Rapport des Dépenses')

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
                <h1 class="text-2xl font-bold text-gray-900">Rapport des Dépenses</h1>
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
            <p class="text-sm font-medium text-gray-500">Total Dépenses</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($summary['total_expenses'], 0, ',', ' ') }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ $summary['expense_count'] }} dépenses</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Approuvées</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($summary['approved_total'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">En Attente</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ number_format($summary['pending_total'], 0, ',', ' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Moyenne/Dépense</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['expense_count'] > 0 ? number_format($summary['total_expenses'] / $summary['expense_count'], 0, ',', ' ') : 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expenses by Category -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Par Catégorie</h2>
            <div class="space-y-3">
                @forelse($expensesByCategory as $expense)
                    @php
                        $maxTotal = $expensesByCategory->max('total') ?: 1;
                        $percentage = ($expense->total / $maxTotal) * 100;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $expense->category->name ?? 'Non catégorisé' }}</span>
                            <span class="font-medium">{{ number_format($expense->total, 0, ',', ' ') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune donnée disponible.</p>
                @endforelse
            </div>
        </div>

        <!-- Monthly Expenses -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Évolution Mensuelle</h2>
            <div class="space-y-3">
                @forelse($expensesByMonth as $month)
                    @php
                        $maxTotal = $expensesByMonth->max('total') ?: 1;
                        $percentage = ($month->total / $maxTotal) * 100;
                        $monthName = \Carbon\Carbon::create($month->year, $month->month)->translatedFormat('M Y');
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $monthName }}</span>
                            <span class="font-medium">{{ number_format($month->total, 0, ',', ' ') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune donnée disponible.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Détail des Dépenses</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('expenses.show', $expense) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ Str::limit($expense->description, 40) }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $expense->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $expense->status === 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $expense->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $expense->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst($expense->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">{{ number_format($expense->total, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucune dépense pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
