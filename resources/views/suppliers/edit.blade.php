@extends('layouts.dashboard')

@section('title', 'Modifier le fournisseur')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux fournisseurs
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $supplier->company_name }}</h1>
    </div>

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'entreprise *</label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $supplier->company_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du contact</label>
                    <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Site web</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $supplier->website) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-1">N° TVA</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $supplier->tax_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">Délai de paiement (jours)</label>
                    <input type="number" name="payment_terms" id="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms ?? 30) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea name="address" id="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('address', $supplier->address) }}</textarea>
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $supplier->city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $supplier->postal_code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                    <select name="country" id="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Sélectionner</option>
                        <option value="FR" {{ old('country', $supplier->country) == 'FR' ? 'selected' : '' }}>France</option>
                        <option value="BE" {{ old('country', $supplier->country) == 'BE' ? 'selected' : '' }}>Belgique</option>
                        <option value="CH" {{ old('country', $supplier->country) == 'CH' ? 'selected' : '' }}>Suisse</option>
                        <option value="LU" {{ old('country', $supplier->country) == 'LU' ? 'selected' : '' }}>Luxembourg</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $supplier->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('suppliers.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
