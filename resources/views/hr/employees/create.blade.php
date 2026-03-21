@extends('layouts.dashboard')

@section('title', 'Nouvel employé')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.employees.index') }}" class="p-2 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nouvel employé</h1>
    </div>

    <form action="{{ route('hr.employees.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- SECTION 1: Informations personnelles -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-primary-50 px-6 py-3 border-b border-primary-100">
                <h2 class="text-lg font-semibold text-primary-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm">1</span>
                    Informations personnelles
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Identité -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('first_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('last_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email personnel</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                        <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sélectionner</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Homme</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femme</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Situation familiale</label>
                        <select name="marital_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sélectionner</option>
                            <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Célibataire</option>
                            <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Marié(e)</option>
                            <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorcé(e)</option>
                            <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Veuf/Veuve</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre d'enfants</label>
                        <input type="number" name="children_count" value="{{ old('children_count', 0) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nationalité</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <!-- Adresse -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Adresse</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <textarea name="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('address') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <input type="text" name="country" value="{{ old('country', 'Sénégal') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Informations professionnelles -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-green-50 px-6 py-3 border-b border-green-100">
                <h2 class="text-lg font-semibold text-green-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-green-600 text-white flex items-center justify-center text-sm">2</span>
                    Informations professionnelles
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Direction, Département, Poste -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                        <select name="direction_id" id="direction_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sélectionner</option>
                            @foreach($directions as $direction)
                                <option value="{{ $direction->id }}" {{ old('direction_id') == $direction->id ? 'selected' : '' }}>{{ $direction->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                        <select name="department_id" id="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sélectionner</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                        <select name="poste_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sélectionner</option>
                            @foreach($postes as $poste)
                                <option value="{{ $poste->id }}" {{ old('poste_id') == $poste->id ? 'selected' : '' }}>{{ $poste->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                        <select name="manager_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Aucun</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                        <input type="email" name="work_email" value="{{ old('work_email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lieu de travail</label>
                        <input type="text" name="work_location" value="{{ old('work_location') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <!-- Contact d'urgence -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Contact d'urgence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du contact</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Relation</label>
                            <select name="emergency_contact_relation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Sélectionner</option>
                                <option value="Conjoint(e)" {{ old('emergency_contact_relation') == 'Conjoint(e)' ? 'selected' : '' }}>Conjoint(e)</option>
                                <option value="Parent" {{ old('emergency_contact_relation') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Frère/Sœur" {{ old('emergency_contact_relation') == 'Frère/Sœur' ? 'selected' : '' }}>Frère/Sœur</option>
                                <option value="Enfant" {{ old('emergency_contact_relation') == 'Enfant' ? 'selected' : '' }}>Enfant</option>
                                <option value="Ami(e)" {{ old('emergency_contact_relation') == 'Ami(e)' ? 'selected' : '' }}>Ami(e)</option>
                                <option value="Autre" {{ old('emergency_contact_relation') == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Informations bancaires -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-amber-50 px-6 py-3 border-b border-amber-100">
                <h2 class="text-lg font-semibold text-amber-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center text-sm">3</span>
                    Informations bancaires
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la banque</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de compte</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                        <input type="text" name="bank_iban" value="{{ old('bank_iban') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('hr.employees.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                Créer l'employé
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const directionSelect = document.getElementById('direction_id');
    const departmentSelect = document.getElementById('department_id');
    const posteSelect = document.querySelector('select[name="poste_id"]');

    // Quand on change la direction
    directionSelect.addEventListener('change', function() {
        const directionId = this.value;
        
        // Reset département et poste
        departmentSelect.innerHTML = '<option value="">Sélectionner</option>';
        posteSelect.innerHTML = '<option value="">Sélectionner</option>';

        if (directionId) {
            fetch(`/hr/api/directions/${directionId}/departments`)
                .then(response => response.json())
                .then(departments => {
                    departments.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }
    });

    // Quand on change le département
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        
        // Reset poste
        posteSelect.innerHTML = '<option value="">Sélectionner</option>';

        if (departmentId) {
            fetch(`/hr/api/departments/${departmentId}/postes`)
                .then(response => response.json())
                .then(postes => {
                    postes.forEach(poste => {
                        const option = document.createElement('option');
                        option.value = poste.id;
                        option.textContent = poste.name;
                        posteSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }
    });
});
</script>
@endsection
