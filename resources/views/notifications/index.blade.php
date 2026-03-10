@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications & Alertes</h1>
            <p class="mt-1 text-sm text-gray-500">{{ count($alerts) }} alerte(s) nécessitant votre attention</p>
        </div>
    </div>

    @if(count($alerts) === 0)
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tout est en ordre !</h3>
            <p class="mt-2 text-gray-500">Aucune alerte à signaler pour le moment.</p>
        </div>
    @else
        <!-- Summary Cards -->
        @php
            $dangerCount = count(array_filter($alerts, fn($a) => $a['type'] === 'danger'));
            $warningCount = count(array_filter($alerts, fn($a) => $a['type'] === 'warning'));
            
            $categories = [
                'invoice_overdue' => ['label' => 'Factures en retard', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'invoice_due_soon' => ['label' => 'Factures bientôt échues', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'quote_expiring' => ['label' => 'Devis expirant bientôt', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'quote_expired' => ['label' => 'Devis expirés', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'low_stock' => ['label' => 'Stock bas', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                'out_of_stock' => ['label' => 'Rupture de stock', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-red-700">{{ $dangerCount }}</p>
                        <p class="text-sm text-red-600">Alertes critiques</p>
                    </div>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-700">{{ $warningCount }}</p>
                        <p class="text-sm text-amber-600">Avertissements</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-700">{{ count($alerts) }}</p>
                        <p class="text-sm text-gray-600">Total alertes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts List -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach($alerts as $alert)
                    <a href="{{ $alert['link'] }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-0.5">
                                @if($alert['type'] === 'danger')
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $alert['type'] === 'danger' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $categories[$alert['category']]['label'] ?? $alert['category'] }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">{{ $alert['message'] }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
