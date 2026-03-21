@extends('layouts.dashboard')

@section('title', 'Bulletins de paie')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bulletins de paie</h1>
            <p class="text-gray-600">Gestion de la paie</p>
        </div>
        <a href="{{ route('hr.payslips.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau bulletin
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Total ce mois</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_this_month'], 0, ',', ' ') }} XOF</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Payés ce mois</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">En attente</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="employee_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Tous les employés</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                @endforeach
            </select>
            <input type="month" name="month" value="{{ request('month') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Tous les statuts</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payé</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net à payer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payslips as $payslip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $payslip->reference }}</td>
                        <td class="px-6 py-4">{{ $payslip->employee->full_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $payslip->period }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ number_format($payslip->net_salary, 0, ',', ' ') }} {{ $payslip->currency }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $payslip->status_color }}-100 text-{{ $payslip->status_color }}-800">
                                {{ $payslip->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($payslip->status === 'draft')
                                    <form action="{{ route('hr.payslips.confirm', $payslip) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-blue-600 hover:text-blue-700" title="Confirmer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                @if($payslip->status === 'confirmed')
                                    <form action="{{ route('hr.payslips.pay', $payslip) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 hover:text-green-700" title="Marquer payé">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('hr.payslips.show', $payslip) }}" class="p-2 text-gray-400 hover:text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucun bulletin de paie</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($payslips->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $payslips->links() }}</div>
        @endif
    </div>
</div>
@endsection
