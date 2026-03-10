@extends('layouts.dashboard')

@section('title', 'Rapports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Rapports</h1>
        <p class="mt-1 text-sm text-gray-500">Analysez les performances de votre entreprise.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sales Report -->
        <a href="{{ route('reports.sales') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Rapport des Ventes</h3>
                    <p class="text-sm text-gray-500">Factures, revenus, top clients</p>
                </div>
            </div>
        </a>

        <!-- Expenses Report -->
        <a href="{{ route('reports.expenses') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Rapport des Dépenses</h3>
                    <p class="text-sm text-gray-500">Dépenses par catégorie, tendances</p>
                </div>
            </div>
        </a>

        <!-- Profit & Loss -->
        <a href="{{ route('reports.profit-loss') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Compte de Résultat</h3>
                    <p class="text-sm text-gray-500">Revenus, dépenses, bénéfices</p>
                </div>
            </div>
        </a>

        <!-- Receivables -->
        <a href="{{ route('reports.receivables') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Créances Clients</h3>
                    <p class="text-sm text-gray-500">Factures impayées, échéances</p>
                </div>
            </div>
        </a>

        <!-- Inventory -->
        <a href="{{ route('reports.inventory') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">État des Stocks</h3>
                    <p class="text-sm text-gray-500">Inventaire, valeur, alertes</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
