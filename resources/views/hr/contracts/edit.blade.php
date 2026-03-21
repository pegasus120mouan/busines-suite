@extends('layouts.dashboard')

@section('title', 'Modifier ' . $contract->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.contracts.show', $contract) }}" class="p-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $contract->name }}</h1>
    </div>

    <form action="{{ route('hr.contracts.update', $contract) }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Intitulé du contrat *</label>
            <input type="text" name="name" value="{{ old('name', $contract->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de contrat *</label>
                <select name="contract_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="cdi" {{ old('contract_type', $contract->contract_type) == 'cdi' ? 'selected' : '' }}>CDI</option>
                    <option value="cdd" {{ old('contract_type', $contract->contract_type) == 'cdd' ? 'selected' : '' }}>CDD</option>
                    <option value="internship" {{ old('contract_type', $contract->contract_type) == 'internship' ? 'selected' : '' }}>Stage</option>
                    <option value="freelance" {{ old('contract_type', $contract->contract_type) == 'freelance' ? 'selected' : '' }}>Freelance</option>
                    <option value="temporary" {{ old('contract_type', $contract->contract_type) == 'temporary' ? 'selected' : '' }}>Intérim</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="draft" {{ old('status', $contract->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="running" {{ old('status', $contract->status) == 'running' ? 'selected' : '' }}>En cours</option>
                    <option value="expired" {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>Expiré</option>
                    <option value="cancelled" {{ old('status', $contract->status) == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                <input type="date" name="end_date" value="{{ old('end_date', $contract->end_date?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salaire *</label>
                <input type="number" name="wage" value="{{ old('wage', $contract->wage) }}" required step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de salaire</label>
                <select name="wage_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="monthly" {{ old('wage_type', $contract->wage_type) == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                    <option value="hourly" {{ old('wage_type', $contract->wage_type) == 'hourly' ? 'selected' : '' }}>Horaire</option>
                    <option value="daily" {{ old('wage_type', $contract->wage_type) == 'daily' ? 'selected' : '' }}>Journalier</option>
                    <option value="yearly" {{ old('wage_type', $contract->wage_type) == 'yearly' ? 'selected' : '' }}>Annuel</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Avantages</label>
            <textarea name="advantages" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('advantages', $contract->advantages) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('notes', $contract->notes) }}</textarea>
        </div>
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('hr.contracts.show', $contract) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
