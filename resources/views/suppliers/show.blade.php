@extends('layouts.dashboard')

@section('title', $supplier->company_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux fournisseurs
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->company_name }}</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Contact</p>
                        <p class="text-sm font-medium text-gray-900">{{ $supplier->contact_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $supplier->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $supplier->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Site web</p>
                        @if($supplier->website)
                            <a href="{{ $supplier->website }}" target="_blank" class="text-sm font-medium text-primary-600 hover:underline">{{ $supplier->website }}</a>
                        @else
                            <p class="text-sm font-medium text-gray-900">-</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">N° TVA</p>
                        <p class="text-sm font-medium text-gray-900">{{ $supplier->tax_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Délai de paiement</p>
                        <p class="text-sm font-medium text-gray-900">{{ $supplier->payment_terms ?? 30 }} jours</p>
                    </div>
                </div>
            </div>

            @if($supplier->address || $supplier->city)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse</h2>
                    <p class="text-sm text-gray-700">
                        {{ $supplier->address }}<br>
                        {{ $supplier->postal_code }} {{ $supplier->city }}<br>
                        {{ $supplier->country }}
                    </p>
                </div>
            @endif

            @if($supplier->notes)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $supplier->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $supplier->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $supplier->status === 'active' ? 'Actif' : 'Inactif' }}
                </span>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé le</span>
                        <span class="text-gray-900">{{ $supplier->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Modifié le</span>
                        <span class="text-gray-900">{{ $supplier->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
