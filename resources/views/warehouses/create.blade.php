@extends('layouts.dashboard')

@section('title', 'Nouvel entrepôt')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('warehouses.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvel entrepôt</h1>
            <p class="mt-1 text-sm text-gray-500">Créez un nouveau lieu de stockage.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('warehouses.store') }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="WH-001" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <textarea name="address" id="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('address') }}</textarea>
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                <select name="country" id="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Sélectionner</option>
                    <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>France</option>
                    <option value="BE" {{ old('country') == 'BE' ? 'selected' : '' }}>Belgique</option>
                    <option value="CH" {{ old('country') == 'CH' ? 'selected' : '' }}>Suisse</option>
                    <option value="CI" {{ old('country') == 'CI' ? 'selected' : '' }}>Côte d'Ivoire</option>
                    <option value="SN" {{ old('country') == 'SN' ? 'selected' : '' }}>Sénégal</option>
                    <option value="MA" {{ old('country') == 'MA' ? 'selected' : '' }}>Maroc</option>
                </select>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label for="manager_name" class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                <input type="text" name="manager_name" id="manager_name" value="{{ old('manager_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="md:col-span-2 flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="text-sm text-gray-700">Actif</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="text-sm text-gray-700">Entrepôt par défaut</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
            <a href="{{ route('warehouses.index') }}" class="px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">Créer l'entrepôt</button>
        </div>
    </form>
</div>
@endsection
