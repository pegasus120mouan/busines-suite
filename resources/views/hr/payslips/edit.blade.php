@extends('layouts.dashboard')

@section('title', 'Modifier ' . $payslip->reference)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.payslips.show', $payslip) }}" class="p-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $payslip->reference }}</h1>
    </div>

    <form action="{{ route('hr.payslips.update', $payslip) }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="p-4 bg-gray-50 rounded-lg">
            <p class="font-medium">{{ $payslip->employee->full_name }}</p>
            <p class="text-sm text-gray-500">{{ $payslip->period }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Salaire de base *</label>
            <input type="number" name="basic_salary" value="{{ old('basic_salary', $payslip->basic_salary) }}" required step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primes et indemnités</label>
                <input type="number" name="total_allowances" value="{{ old('total_allowances', $payslip->total_allowances) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Heures supplémentaires</label>
                <input type="number" name="overtime_amount" value="{{ old('overtime_amount', $payslip->overtime_amount) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bonus</label>
                <input type="number" name="bonus_amount" value="{{ old('bonus_amount', $payslip->bonus_amount) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Retenues</label>
                <input type="number" name="total_deductions" value="{{ old('total_deductions', $payslip->total_deductions) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('notes', $payslip->notes) }}</textarea>
        </div>
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('hr.payslips.show', $payslip) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
