@extends('layouts.dashboard')

@section('title', 'Modifier le client')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux clients
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier le client</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $customer->display_name }}</p>
    </div>

    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Type Selection -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Type de client</h2>
            <div class="grid grid-cols-2 gap-4">
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-primary-500 has-[:checked]:border-primary-600 has-[:checked]:ring-2 has-[:checked]:ring-primary-600">
                    <input type="radio" name="type" value="individual" class="sr-only" {{ old('type', $customer->type) === 'individual' ? 'checked' : '' }} onchange="toggleCompanyFields()">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">Particulier</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">Personne physique</span>
                        </span>
                    </span>
                </label>
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-primary-500 has-[:checked]:border-primary-600 has-[:checked]:ring-2 has-[:checked]:ring-primary-600">
                    <input type="radio" name="type" value="company" class="sr-only" {{ old('type', $customer->type) === 'company' ? 'checked' : '' }} onchange="toggleCompanyFields()">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">Entreprise</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">Personne morale</span>
                        </span>
                    </span>
                </label>
            </div>
        </div>

        <!-- Company Info -->
        <div id="company-fields" class="bg-white rounded-xl border border-gray-200 p-6 {{ old('type', $customer->type) !== 'company' ? 'hidden' : '' }}">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations entreprise</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Raison sociale *</label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $customer->company_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('company_name') border-red-500 @enderror">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-1">N° TVA / SIRET</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $customer->tax_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Site web</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $customer->website) }}" placeholder="https://" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de contact</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom <span id="first_name_required">{{ $customer->type === 'individual' ? '*' : '' }}</span></label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $customer->first_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom <span id="last_name_required">{{ $customer->type === 'individual' ? '*' : '' }}</span></label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $customer->last_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                    <input type="tel" name="mobile" id="mobile" value="{{ old('mobile', $customer->mobile) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Billing Address -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse de facturation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea name="billing_address" id="billing_address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('billing_address', $customer->billing_address) }}</textarea>
                </div>
                <div>
                    <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $customer->billing_city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                    <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code', $customer->billing_postal_code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                    <select name="billing_country" id="billing_country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Sélectionner</option>
                        <option value="FR" {{ old('billing_country', $customer->billing_country) === 'FR' ? 'selected' : '' }}>France</option>
                        <option value="BE" {{ old('billing_country', $customer->billing_country) === 'BE' ? 'selected' : '' }}>Belgique</option>
                        <option value="CH" {{ old('billing_country', $customer->billing_country) === 'CH' ? 'selected' : '' }}>Suisse</option>
                        <option value="LU" {{ old('billing_country', $customer->billing_country) === 'LU' ? 'selected' : '' }}>Luxembourg</option>
                        <option value="DE" {{ old('billing_country', $customer->billing_country) === 'DE' ? 'selected' : '' }}>Allemagne</option>
                        <option value="ES" {{ old('billing_country', $customer->billing_country) === 'ES' ? 'selected' : '' }}>Espagne</option>
                        <option value="IT" {{ old('billing_country', $customer->billing_country) === 'IT' ? 'selected' : '' }}>Italie</option>
                        <option value="GB" {{ old('billing_country', $customer->billing_country) === 'GB' ? 'selected' : '' }}>Royaume-Uni</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
            <textarea name="notes" id="notes" rows="3" placeholder="Notes internes sur ce client..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $customer->notes) }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('customers.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                Enregistrer
            </button>
        </div>
    </form>
</div>

<script>
function toggleCompanyFields() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const companyFields = document.getElementById('company-fields');
    const firstNameRequired = document.getElementById('first_name_required');
    const lastNameRequired = document.getElementById('last_name_required');
    
    if (type === 'company') {
        companyFields.classList.remove('hidden');
        firstNameRequired.textContent = '';
        lastNameRequired.textContent = '';
    } else {
        companyFields.classList.add('hidden');
        firstNameRequired.textContent = '*';
        lastNameRequired.textContent = '*';
    }
}

document.addEventListener('DOMContentLoaded', toggleCompanyFields);
</script>
@endsection
