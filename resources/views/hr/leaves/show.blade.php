@extends('layouts.dashboard')

@section('title', 'Détails du congé')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('hr.leaves.index') }}" class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Demande de congé</h1>
        </div>
        <span class="px-3 py-1 text-sm rounded-full bg-{{ $leave->status_color }}-100 text-{{ $leave->status_color }}-800">
            {{ $leave->status_label }}
        </span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Employé</p>
                <p class="font-medium">{{ $leave->employee->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Type de congé</p>
                <span class="px-2 py-1 text-xs rounded-full" style="background-color: {{ $leave->leaveType->color }}20; color: {{ $leave->leaveType->color }}">
                    {{ $leave->leaveType->name }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date de début</p>
                <p class="font-medium">{{ $leave->start_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date de fin</p>
                <p class="font-medium">{{ $leave->end_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Nombre de jours</p>
                <p class="font-medium">{{ $leave->number_of_days }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date de demande</p>
                <p class="font-medium">{{ $leave->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if($leave->reason)
        <div>
            <p class="text-sm text-gray-500">Motif</p>
            <p class="text-gray-700">{{ $leave->reason }}</p>
        </div>
        @endif

        @if($leave->approver)
        <div class="border-t pt-4">
            <p class="text-sm text-gray-500">Traité par</p>
            <p class="font-medium">{{ $leave->approver->name }} le {{ $leave->approved_at->format('d/m/Y H:i') }}</p>
            @if($leave->approval_notes)
                <p class="text-gray-600 mt-1">{{ $leave->approval_notes }}</p>
            @endif
        </div>
        @endif

        @if($leave->status === 'pending')
        <div class="flex gap-4 pt-4 border-t">
            <form action="{{ route('hr.leaves.approve', $leave) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Approuver
                </button>
            </form>
            <form action="{{ route('hr.leaves.refuse', $leave) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Refuser
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
