@extends('layouts.dashboard')

@section('title', 'Modifier le rôle - ' . $role->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('hr.roles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux rôles
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Modifier le rôle</h1>
    </div>

    <form action="{{ route('hr.roles.update', $role) }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du rôle *</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" value="{{ old('code', $role->code) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Niveau hiérarchique</label>
            <input type="number" name="level" value="{{ old('level', $role->level) }}" min="0" max="100"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
            <input type="color" name="color" value="{{ old('color', $role->color ?? '#3B82F6') }}"
                class="w-20 h-10 border border-gray-300 rounded-lg cursor-pointer">
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-medium text-gray-900 mb-4">Permissions</h3>
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="is_manager" value="1" {{ old('is_manager', $role->is_manager) ? 'checked' : '' }}
                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div>
                        <span class="font-medium text-gray-900">Rôle de manager</span>
                        <p class="text-sm text-gray-500">Ce rôle est un rôle de management</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="can_approve_leaves" value="1" {{ old('can_approve_leaves', $role->can_approve_leaves) ? 'checked' : '' }}
                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div>
                        <span class="font-medium text-gray-900">Peut approuver les congés</span>
                        <p class="text-sm text-gray-500">Autorise à valider ou refuser les demandes de congés</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="can_manage_team" value="1" {{ old('can_manage_team', $role->can_manage_team) ? 'checked' : '' }}
                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <div>
                        <span class="font-medium text-gray-900">Peut gérer l'équipe</span>
                        <p class="text-sm text-gray-500">Autorise à gérer les membres de l'équipe</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                    class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="font-medium text-gray-900">Rôle actif</span>
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('hr.roles.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
