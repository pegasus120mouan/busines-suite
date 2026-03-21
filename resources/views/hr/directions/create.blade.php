@extends('layouts.dashboard')

@section('title', 'Nouvelle direction')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.directions.index') }}" class="p-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nouvelle direction</h1>
    </div>

    <form action="{{ route('hr.directions.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la direction *</label>
            <input type="text" name="name" value="{{ old('name') }}" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                placeholder="Ex: Direction des Ressources Humaines">
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Le code sera généré automatiquement</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Description de la direction...">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
            <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" class="w-20 h-10 border border-gray-300 rounded-lg cursor-pointer">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="is_active" class="text-sm text-gray-700">Direction active</label>
        </div>
        <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
            <a href="{{ route('hr.directions.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Créer</button>
        </div>
    </form>
</div>
@endsection
