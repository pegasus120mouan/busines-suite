@extends('layouts.dashboard')

@section('title', $contract->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('hr.contracts.index') }}" class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $contract->name }}</h1>
                <p class="text-gray-600">{{ $contract->reference }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 text-sm rounded-full bg-{{ $contract->status_color }}-100 text-{{ $contract->status_color }}-800">
                {{ $contract->status_label }}
            </span>
            <a href="{{ route('hr.contracts.edit', $contract) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Employé</p>
                    <p class="font-medium">{{ $contract->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Type de contrat</p>
                    <p class="font-medium">{{ $contract->contract_type_label }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Département</p>
                    <p class="font-medium">{{ $contract->department?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Poste</p>
                    <p class="font-medium">{{ $contract->jobPosition?->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Durée</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Date de début</p>
                    <p class="font-medium">{{ $contract->start_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date de fin</p>
                    <p class="font-medium">{{ $contract->end_date?->format('d/m/Y') ?? 'Indéterminée' }}</p>
                </div>
                @if($contract->trial_end_date)
                <div>
                    <p class="text-sm text-gray-500">Fin période d'essai</p>
                    <p class="font-medium">{{ $contract->trial_end_date->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($contract->days_until_end !== null)
                <div>
                    <p class="text-sm text-gray-500">Jours restants</p>
                    <p class="font-medium {{ $contract->days_until_end < 30 ? 'text-amber-600' : '' }}">{{ $contract->days_until_end }} jours</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Rémunération</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Salaire</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($contract->wage, 0, ',', ' ') }} {{ $contract->currency }}</p>
                    <p class="text-sm text-gray-500">{{ $contract->wage_type == 'monthly' ? 'par mois' : ($contract->wage_type == 'hourly' ? 'par heure' : ($contract->wage_type == 'daily' ? 'par jour' : 'par an')) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Heures par semaine</p>
                    <p class="font-medium">{{ $contract->hours_per_week }}h</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Avantages</h2>
            <p class="text-gray-700">{{ $contract->advantages ?? 'Aucun avantage spécifié' }}</p>
        </div>
    </div>

    @if($contract->notes)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
        <p class="text-gray-700">{{ $contract->notes }}</p>
    </div>
    @endif

    @if($contract->status === 'draft')
    <div class="flex justify-end">
        <form action="{{ route('hr.contracts.activate', $contract) }}" method="POST">
            @csrf
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Activer le contrat
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
