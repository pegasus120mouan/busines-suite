@extends('layouts.dashboard')

@section('title', request('parent_id') ? 'Nouveau sous-rôle' : 'Nouveau rôle RH')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ request('parent_id') ? route('hr.roles.show', request('parent_id')) : route('hr.roles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">
            @if(request('parent_id'))
                @php $parentRole = $parentRoles->firstWhere('id', request('parent_id')); @endphp
                Nouveau sous-rôle de {{ $parentRole?->name ?? 'Directeur' }}
            @else
                Nouveau rôle RH
            @endif
        </h1>
        @if(request('parent_id') && $parentRole)
            <p class="text-gray-500 mt-1">Ex: {{ $parentRole->name }} Ressources Humaines, {{ $parentRole->name }} Financier, {{ $parentRole->name }} SI...</p>
        @endif
    </div>

    <form action="{{ route('hr.roles.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf
        <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">

        @if(!request('parent_id'))
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rôle parent (optionnel)</label>
            <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Aucun (rôle principal)</option>
                @foreach($parentRoles as $role)
                    <option value="{{ $role->id }}" {{ old('parent_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Sélectionnez un rôle parent pour créer un sous-rôle (ex: Directeur → Directeur RH)</p>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du rôle *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                placeholder="Ex: Directeur Ressources Humaines">
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Le code sera généré automatiquement</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Description du rôle et de ses responsabilités...">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                <select name="direction_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Toutes les directions</option>
                    @foreach($directions as $direction)
                        <option value="{{ $direction->id }}" {{ old('direction_id') == $direction->id ? 'selected' : '' }}>
                            {{ $direction->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Laissez vide si ce rôle s'applique à toutes les directions</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Niveau hiérarchique</label>
                <input type="number" name="level" value="{{ old('level', 0) }}" min="0" max="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="mt-1 text-xs text-gray-500">Plus le niveau est élevé, plus le rôle est haut dans la hiérarchie</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
            <input type="color" name="color" value="{{ old('color', '#3B82F6') }}"
                class="w-20 h-10 border border-gray-300 rounded-lg cursor-pointer">
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-medium text-gray-900 mb-4">Permissions</h3>
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="is_manager" value="1" {{ old('is_manager') ? 'checked' : '' }}
                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div>
                        <span class="font-medium text-gray-900">Rôle de manager</span>
                        <p class="text-sm text-gray-500">Ce rôle est un rôle de management avec des responsabilités d'encadrement</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="can_approve_leaves" value="1" {{ old('can_approve_leaves') ? 'checked' : '' }}
                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div>
                        <span class="font-medium text-gray-900">Peut approuver les congés</span>
                        <p class="text-sm text-gray-500">Autorise à valider ou refuser les demandes de congés de l'équipe</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="can_manage_team" value="1" {{ old('can_manage_team') ? 'checked' : '' }}
                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div>
                        <span class="font-medium text-gray-900">Peut gérer l'équipe</span>
                        <p class="text-sm text-gray-500">Autorise à voir et gérer les informations des membres de l'équipe</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="font-medium text-gray-900">Rôle actif</span>
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('hr.roles.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                Créer le rôle
            </button>
        </div>
    </form>
</div>
@endsection
