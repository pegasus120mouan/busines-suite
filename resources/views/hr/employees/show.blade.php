@extends('layouts.dashboard')

@section('title', $employee->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('hr.employees.index') }}" class="p-2 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-700 font-bold text-xl">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
                    <p class="text-gray-600">{{ $employee->employee_number }} • {{ $employee->jobPosition?->name ?? 'Poste non défini' }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $employee->status_color }}-100 text-{{ $employee->status_color }}-800">
                {{ $employee->status_label }}
            </span>
            <a href="{{ route('hr.employees.edit', $employee) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $employee->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium">{{ $employee->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Mobile</p>
                        <p class="font-medium">{{ $employee->mobile ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date de naissance</p>
                        <p class="font-medium">{{ $employee->birth_date?->format('d/m/Y') ?? '-' }} @if($employee->age)({{ $employee->age }} ans)@endif</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Genre</p>
                        <p class="font-medium">{{ $employee->gender == 'male' ? 'Homme' : ($employee->gender == 'female' ? 'Femme' : '-') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Situation familiale</p>
                        <p class="font-medium">
                            @switch($employee->marital_status)
                                @case('single') Célibataire @break
                                @case('married') Marié(e) @break
                                @case('divorced') Divorcé(e) @break
                                @case('widowed') Veuf/Veuve @break
                                @default -
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Enfants</p>
                        <p class="font-medium">{{ $employee->children_count }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nationalité</p>
                        <p class="font-medium">{{ $employee->nationality ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Adresse -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse</h2>
                <p class="text-gray-700">
                    {{ $employee->address ?? '-' }}<br>
                    {{ $employee->postal_code }} {{ $employee->city }}<br>
                    {{ $employee->country }}
                </p>
            </div>

            <!-- Contrats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Contrats</h2>
                    <a href="{{ route('hr.contracts.create', ['employee_id' => $employee->id]) }}" class="text-sm text-primary-600 hover:text-primary-700">+ Nouveau contrat</a>
                </div>
                @if($employee->contracts->count() > 0)
                    <div class="space-y-3">
                        @foreach($employee->contracts as $contract)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $contract->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $contract->contract_type_label }} • {{ $contract->start_date->format('d/m/Y') }} - {{ $contract->end_date?->format('d/m/Y') ?? 'Indéterminé' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $contract->status_color }}-100 text-{{ $contract->status_color }}-800">{{ $contract->status_label }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Aucun contrat</p>
                @endif
            </div>

            <!-- Congés récents -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Congés récents</h2>
                    <a href="{{ route('hr.leaves.create') }}" class="text-sm text-primary-600 hover:text-primary-700">+ Nouvelle demande</a>
                </div>
                @if($employee->leaves->count() > 0)
                    <div class="space-y-3">
                        @foreach($employee->leaves->take(5) as $leave)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $leave->leaveType->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }} ({{ $leave->number_of_days }} jours)</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $leave->status_color }}-100 text-{{ $leave->status_color }}-800">{{ $leave->status_label }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Aucun congé</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informations professionnelles -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Poste</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Département</p>
                        <p class="font-medium">{{ $employee->department?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Poste</p>
                        <p class="font-medium">{{ $employee->jobPosition?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Manager</p>
                        <p class="font-medium">{{ $employee->manager?->full_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date d'embauche</p>
                        <p class="font-medium">{{ $employee->hire_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ancienneté</p>
                        <p class="font-medium">{{ $employee->seniority ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact professionnel -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact professionnel</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $employee->work_email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium">{{ $employee->work_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Lieu de travail</p>
                        <p class="font-medium">{{ $employee->work_location ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact d'urgence -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact d'urgence</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Nom</p>
                        <p class="font-medium">{{ $employee->emergency_contact_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium">{{ $employee->emergency_contact_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informations bancaires -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations bancaires</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Banque</p>
                        <p class="font-medium">{{ $employee->bank_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Numéro de compte</p>
                        <p class="font-medium">{{ $employee->bank_account_number ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
