@extends('layouts.dashboard')

@section('title', 'Modifier le poste - ' . $poste->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('hr.postes.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux postes
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Modifier le poste</h1>
    </div>

    <form action="{{ route('hr.postes.update', $poste) }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du poste *</label>
                <input type="text" name="name" value="{{ old('name', $poste->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" value="{{ $poste->code }}" disabled
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Département</label>
            <select name="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Aucun département</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $poste->department_id) == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Salaire de base (XOF) *</label>
            <input type="number" name="salaire_base" value="{{ old('salaire_base', $poste->salaire_base) }}" required min="0" step="1000"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('salaire_base') border-red-500 @enderror">
            @error('salaire_base')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $poste->description) }}</textarea>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $poste->is_active) ? 'checked' : '' }}
                    class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="font-medium text-gray-900">Actif</span>
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('hr.postes.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
