@extends('layouts.dashboard')

@section('title', 'Contrats')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contrats</h1>
            <p class="text-gray-600">Gérez les contrats de travail</p>
        </div>
        <a href="{{ route('hr.contracts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau contrat
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500">Total contrats</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-2xl font-bold text-green-600">{{ $stats['running'] }}</p>
            <p class="text-sm text-gray-500">En cours</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['expiring_soon'] }}</p>
            <p class="text-sm text-gray-500">Expirent bientôt</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $contract->reference }}</td>
                        <td class="px-6 py-4">{{ $contract->employee->full_name }}</td>
                        <td class="px-6 py-4">{{ $contract->contract_type_label }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $contract->start_date->format('d/m/Y') }} - {{ $contract->end_date?->format('d/m/Y') ?? 'Indéterminé' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ number_format($contract->wage, 0, ',', ' ') }} {{ $contract->currency }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $contract->status_color }}-100 text-{{ $contract->status_color }}-800">
                                {{ $contract->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($contract->status === 'draft')
                                    <form action="{{ route('hr.contracts.activate', $contract) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 hover:text-green-700" title="Activer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('hr.contracts.show', $contract) }}" class="p-2 text-gray-400 hover:text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('hr.contracts.edit', $contract) }}" class="p-2 text-gray-400 hover:text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Aucun contrat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($contracts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
@endsection
