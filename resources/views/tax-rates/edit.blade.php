@extends('layouts.dashboard')

@section('title', 'Modifier le Taux de TVA')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('tax-rates.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier le Taux de TVA</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $taxRate->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('tax-rates.update', $taxRate) }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $taxRate->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="rate" class="block text-sm font-medium text-gray-700 mb-1">Taux (%) *</label>
            <input type="number" name="rate" id="rate" value="{{ old('rate', $taxRate->rate) }}" step="0.01" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
            @error('rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $taxRate->is_default) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="text-sm text-gray-700">Taux par défaut</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $taxRate->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="text-sm text-gray-700">Actif</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
            <a href="{{ route('tax-rates.index') }}" class="px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
