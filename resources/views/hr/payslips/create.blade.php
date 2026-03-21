@extends('layouts.dashboard')

@section('title', 'Nouveau bulletin de paie')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.payslips.index') }}" class="p-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nouveau bulletin de paie</h1>
    </div>

    <form action="{{ route('hr.payslips.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employé *</label>
            <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Sélectionner</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                @endforeach
            </select>
            <p class="text-sm text-gray-500 mt-1">Seuls les employés avec un contrat actif sont affichés</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                <input type="date" name="date_from" value="{{ old('date_from', now()->startOfMonth()->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                <input type="date" name="date_to" value="{{ old('date_to', now()->endOfMonth()->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <hr class="my-4">
        <h3 class="font-medium text-gray-900">Rémunération</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Salaire de base *</label>
            <input type="number" name="basic_salary" value="{{ old('basic_salary') }}" required step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primes et indemnités</label>
                <input type="number" name="total_allowances" value="{{ old('total_allowances', 0) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Heures supplémentaires</label>
                <input type="number" name="overtime_amount" value="{{ old('overtime_amount', 0) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bonus</label>
                <input type="number" name="bonus_amount" value="{{ old('bonus_amount', 0) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Retenues</label>
                <input type="number" name="total_deductions" value="{{ old('total_deductions', 0) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('notes') }}</textarea>
        </div>
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('hr.payslips.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Créer</button>
        </div>
    </form>
</div>
@endsection
