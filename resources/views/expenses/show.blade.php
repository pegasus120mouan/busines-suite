@extends('layouts.dashboard')

@section('title', 'Dépense')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux dépenses
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ Str::limit($expense->description, 50) }}</h1>
            <div class="flex items-center gap-3 mt-2">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'paid' => 'bg-blue-100 text-blue-800',
                    ];
                    $statusLabels = [
                        'pending' => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Rejetée',
                        'paid' => 'Payée',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$expense->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$expense->status] ?? $expense->status }}
                </span>
            </div>
        </div>
        <div class="flex gap-3">
            @if($expense->status === 'pending')
                <form method="POST" action="{{ route('expenses.approve', $expense) }}" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rejeter
                    </button>
                </form>
                <form method="POST" action="{{ route('expenses.approve', $expense) }}" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approuver
                    </button>
                </form>
            @endif
            @if(in_array($expense->status, ['pending', 'rejected']))
                <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Montant total</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($expense->total, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                @if($expense->tax_amount > 0)
                    <p class="text-sm text-gray-500 mt-1">
                        HT: {{ number_format($expense->amount, 0, ',', ' ') }} {{ $currencySymbol }} | TVA: {{ number_format($expense->tax_amount, 0, ',', ' ') }} {{ $currencySymbol }}
                    </p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Date</p>
                <p class="text-lg font-medium text-gray-900">{{ $expense->expense_date->format('d/m/Y') }}</p>
            </div>
        </div>

        <hr class="my-6">

        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Catégorie</p>
                @if($expense->category)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-sm font-medium mt-1" style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }}">
                        {{ $expense->category->name }}
                    </span>
                @else
                    <p class="text-sm text-gray-900">-</p>
                @endif
            </div>
            @if($expense->payment_method)
                <div>
                    <p class="text-sm text-gray-500">Mode de paiement</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">
                        @php
                            $methods = [
                                'cash' => 'Espèces',
                                'credit_card' => 'Carte bancaire',
                                'bank_transfer' => 'Virement',
                                'check' => 'Chèque',
                                'other' => 'Autre',
                            ];
                        @endphp
                        {{ $methods[$expense->payment_method] ?? $expense->payment_method }}
                    </p>
                </div>
            @endif
            @if($expense->reference)
                <div>
                    <p class="text-sm text-gray-500">Référence</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $expense->reference }}</p>
                </div>
            @endif
            <div>
                <p class="text-sm text-gray-500">Soumis par</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $expense->user?->name ?? 'N/A' }}</p>
            </div>
            @if($expense->is_billable)
                <div>
                    <p class="text-sm text-gray-500">Refacturable</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">Oui</span>
                </div>
            @endif
        </div>

        @if($expense->notes)
            <hr class="my-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $expense->notes }}</p>
            </div>
        @endif

        @if($expense->approved_at)
            <hr class="my-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">
                    {{ $expense->status === 'approved' ? 'Approuvée' : 'Rejetée' }} par 
                    <span class="font-medium text-gray-900">{{ $expense->approvedBy?->name ?? 'N/A' }}</span>
                    le {{ $expense->approved_at->format('d/m/Y à H:i') }}
                </p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Description complète</h3>
        <p class="text-gray-700">{{ $expense->description }}</p>
    </div>
</div>
@endsection
