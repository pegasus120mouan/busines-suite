@extends('layouts.dashboard')

@section('title', 'Rôle - ' . $role->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('hr.roles.index') }}" class="hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Rôles
                </a>
                @if($role->parent)
                    <span>/</span>
                    <a href="{{ route('hr.roles.show', $role->parent) }}" class="hover:text-gray-700">{{ $role->parent->name }}</a>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: {{ $role->color ?? '#6B7280' }}20">
                    <svg class="w-6 h-6" style="color: {{ $role->color ?? '#6B7280' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $role->name }}</h1>
                    <p class="text-gray-500">
                        @if($role->code) {{ $role->code }} - @endif
                        Niveau {{ $role->level }}
                        @if($role->children->count() > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">{{ $role->children->count() }} sous-rôles</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(!$role->parent_id)
            <a href="{{ route('hr.roles.create', ['parent_id' => $role->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter un sous-rôle
            </a>
            @endif
            <a href="{{ route('hr.roles.edit', $role) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    @if(!$role->parent_id && $role->children->count() > 0)
    <!-- Sous-rôles (ex: Directeur RH, Directeur Financier, etc.) -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                Sous-rôles de {{ $role->name }}
                <span class="text-sm font-normal text-gray-500">({{ $role->children->count() }})</span>
            </h2>
            <a href="{{ route('hr.roles.create', ['parent_id' => $role->id]) }}" class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($role->children as $childRole)
                <a href="{{ route('hr.roles.show', $childRole) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $childRole->color ?? $role->color ?? '#6B7280' }}20">
                            <svg class="w-5 h-5" style="color: {{ $childRole->color ?? $role->color ?? '#6B7280' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $childRole->name }}</p>
                            <p class="text-sm text-gray-500">{{ $childRole->department?->name ?? 'Tous départements' }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                @if($childRole->assignments->count() > 0)
                                    <span class="text-xs text-green-600">{{ $childRole->assignments->count() }} assigné(s)</span>
                                @else
                                    <span class="text-xs text-gray-400">Non assigné</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Liste des assignations -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Assignations ({{ $role->assignments->count() }})
                </h2>
                @if($role->assignments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Département</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supérieur</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Depuis</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($role->assignments as $assignment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                    <span class="text-primary-700 text-xs font-medium">{{ substr($assignment->employee->first_name, 0, 1) }}{{ substr($assignment->employee->last_name, 0, 1) }}</span>
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $assignment->employee->full_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $assignment->department?->name ?? 'Tous' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $assignment->reportsTo?->full_name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $assignment->start_date?->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <form action="{{ route('hr.roles.assignments.destroy', [$role, $assignment]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette assignation ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-red-400 hover:text-red-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Aucune assignation pour ce rôle</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Assignations actives</span>
                        <span class="font-bold text-gray-900">{{ $role->assignments->where('is_active', true)->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Employés (direct)</span>
                        <span class="font-bold text-gray-900">{{ $role->employees->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé le</span>
                        <span class="text-gray-900">{{ $role->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Modifié le</span>
                        <span class="text-gray-900">{{ $role->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            @if($role->employees->count() === 0 && $role->assignments->count() === 0)
                <form action="{{ route('hr.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 font-medium rounded-lg hover:bg-red-100 transition">
                        Supprimer ce rôle
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
