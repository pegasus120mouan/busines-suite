@extends('layouts.dashboard')

@section('title', 'Pointage')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pointage</h1>
            <p class="text-gray-600">Suivi des présences</p>
        </div>
        <a href="{{ route('hr.attendances.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau pointage
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['present_today'] }}</p>
                    <p class="text-sm text-gray-500">Présents aujourd'hui</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['absent_today'] }}</p>
                    <p class="text-sm text-gray-500">Absents</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['late_today'] }}</p>
                    <p class="text-sm text-gray-500">Retards</p>
                </div>
            </div>
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
            <input type="date" name="date" value="{{ request('date') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Tous les statuts</option>
                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Présent</option>
                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Retard</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrée</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sortie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heures</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $attendance->employee->full_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $attendance->date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $attendance->check_in ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $attendance->check_out ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $attendance->worked_hours ? number_format($attendance->worked_hours, 1) . 'h' : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $attendance->status_color }}-100 text-{{ $attendance->status_color }}-800">
                                {{ $attendance->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('hr.attendances.destroy', $attendance) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce pointage ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Aucun pointage</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $attendances->links() }}</div>
        @endif
    </div>
</div>
@endsection
