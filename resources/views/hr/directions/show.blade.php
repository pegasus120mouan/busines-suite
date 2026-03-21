@extends('layouts.dashboard')

@section('title', 'Direction - ' . $direction->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('hr.directions.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour aux directions
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: {{ $direction->color ?? '#3B82F6' }}20">
                    <svg class="w-6 h-6" style="color: {{ $direction->color ?? '#3B82F6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $direction->name }}</h1>
                    <p class="text-gray-500">{{ $direction->code }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('hr.departments.create', ['direction_id' => $direction->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter un département
            </a>
            <a href="{{ route('hr.directions.edit', $direction) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Code</p>
                        <p class="font-medium text-gray-900 font-mono">{{ $direction->code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        @if($direction->is_active)
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Actif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactif</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Départements</p>
                        <p class="font-medium text-gray-900">{{ $direction->departments->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Employés</p>
                        <p class="font-medium text-gray-900">{{ $direction->departments->sum(fn($d) => $d->employees->count()) }}</p>
                    </div>
                </div>
                @if($direction->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">Description</p>
                        <p class="text-gray-700">{{ $direction->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Départements -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Départements</h2>
                    <a href="{{ route('hr.departments.create', ['direction_id' => $direction->id]) }}" class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter
                    </a>
                </div>
                @if($direction->departments->isEmpty())
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-gray-500">Aucun département dans cette direction</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($direction->departments->where('parent_id', null) as $department)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $department->color ?? '#6B7280' }}20">
                                            <svg class="w-5 h-5" style="color: {{ $department->color ?? '#6B7280' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <a href="{{ route('hr.departments.show', $department) }}" class="font-medium text-gray-900 hover:text-primary-600">
                                                {{ $department->name }}
                                            </a>
                                            <p class="text-sm text-gray-500">
                                                {{ $department->employees->count() }} employé(s)
                                                @if($department->children->count() > 0)
                                                    • {{ $department->children->count() }} sous-département(s)
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('hr.departments.show', $department) }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                                @if($department->children->count() > 0)
                                    <div class="mt-3 pl-12 space-y-2">
                                        @foreach($department->children as $child)
                                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                                    </svg>
                                                    <a href="{{ route('hr.departments.show', $child) }}" class="text-sm text-gray-700 hover:text-primary-600">
                                                        {{ $child->name }}
                                                    </a>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $child->employees->count() }} employé(s)</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Statistiques</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Départements</span>
                        <span class="font-medium text-gray-900">{{ $direction->departments->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Sous-départements</span>
                        <span class="font-medium text-gray-900">{{ $direction->departments->sum(fn($d) => $d->children->count()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total employés</span>
                        <span class="font-medium text-gray-900">{{ $direction->departments->sum(fn($d) => $d->employees->count()) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-2">
                    <a href="{{ route('hr.departments.create', ['direction_id' => $direction->id]) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter un département
                    </a>
                    <a href="{{ route('hr.directions.edit', $direction) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier la direction
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
