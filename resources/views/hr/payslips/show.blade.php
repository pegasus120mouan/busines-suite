@extends('layouts.dashboard')

@section('title', $payslip->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('hr.payslips.index') }}" class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $payslip->reference }}</h1>
                <p class="text-gray-600">{{ $payslip->name }}</p>
            </div>
        </div>
        <span class="px-3 py-1 text-sm rounded-full bg-{{ $payslip->status_color }}-100 text-{{ $payslip->status_color }}-800">
            {{ $payslip->status_label }}
        </span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-500">Employé</p>
                <p class="font-medium text-lg">{{ $payslip->employee->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Période</p>
                <p class="font-medium">{{ $payslip->period }}</p>
            </div>
        </div>

        <hr class="my-6">

        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Salaire de base</span>
                <span class="font-medium">{{ number_format($payslip->basic_salary, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>
            @if($payslip->total_allowances > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Primes et indemnités</span>
                <span class="font-medium text-green-600">+ {{ number_format($payslip->total_allowances, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>
            @endif
            @if($payslip->overtime_amount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Heures supplémentaires</span>
                <span class="font-medium text-green-600">+ {{ number_format($payslip->overtime_amount, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>
            @endif
            @if($payslip->bonus_amount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Bonus</span>
                <span class="font-medium text-green-600">+ {{ number_format($payslip->bonus_amount, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>
            @endif

            <hr>

            <div class="flex justify-between">
                <span class="text-gray-600">Salaire brut</span>
                <span class="font-medium">{{ number_format($payslip->gross_salary, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>

            @if($payslip->total_deductions > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Retenues</span>
                <span class="font-medium text-red-600">- {{ number_format($payslip->total_deductions, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>
            @endif

            <hr>

            <div class="flex justify-between text-lg">
                <span class="font-semibold text-gray-900">Net à payer</span>
                <span class="font-bold text-primary-600">{{ number_format($payslip->net_salary, 0, ',', ' ') }} {{ $payslip->currency }}</span>
            </div>
        </div>

        @if($payslip->payment_date)
        <div class="mt-6 p-4 bg-green-50 rounded-lg">
            <p class="text-sm text-green-800">
                <strong>Payé le :</strong> {{ $payslip->payment_date->format('d/m/Y') }}
                @if($payslip->payment_method)
                    - {{ $payslip->payment_method }}
                @endif
            </p>
        </div>
        @endif

        @if($payslip->notes)
        <div class="mt-6">
            <p class="text-sm text-gray-500">Notes</p>
            <p class="text-gray-700">{{ $payslip->notes }}</p>
        </div>
        @endif
    </div>

    <div class="flex justify-end gap-4">
        @if($payslip->status === 'draft')
            <form action="{{ route('hr.payslips.confirm', $payslip) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Confirmer
                </button>
            </form>
        @endif
        @if($payslip->status === 'confirmed')
            <form action="{{ route('hr.payslips.pay', $payslip) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Marquer comme payé
                </button>
            </form>
        @endif
        <a href="{{ route('hr.payslips.edit', $payslip) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Modifier
        </a>
    </div>
</div>
@endsection
