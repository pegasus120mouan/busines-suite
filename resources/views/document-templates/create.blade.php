@extends('layouts.dashboard')

@section('title', 'Nouveau modèle de document')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('document-templates.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau modèle de document</h1>
            <p class="mt-1 text-sm text-gray-500">Créez un modèle personnalisé pour vos documents</p>
        </div>
    </div>

    <form method="POST" action="{{ route('document-templates.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Settings -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du modèle *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de document *</label>
                            <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="invoice" {{ old('type', request('type')) == 'invoice' ? 'selected' : '' }}>Facture</option>
                                <option value="quote" {{ old('type', request('type')) == 'quote' ? 'selected' : '' }}>Devis</option>
                                <option value="reminder" {{ old('type', request('type')) == 'reminder' ? 'selected' : '' }}>Relance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contenu du document</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="header" class="block text-sm font-medium text-gray-700 mb-1">En-tête personnalisé</label>
                            <textarea name="header" id="header" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Texte affiché en haut du document">{{ old('header') }}</textarea>
                        </div>
                        <div>
                            <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">Conditions générales</label>
                            <textarea name="terms" id="terms" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Conditions de vente, délais de paiement...">{{ old('terms') }}</textarea>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes par défaut</label>
                            <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Notes additionnelles">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label for="footer" class="block text-sm font-medium text-gray-700 mb-1">Pied de page</label>
                            <textarea name="footer" id="footer" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Informations légales, mentions obligatoires...">{{ old('footer') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Apparence</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="color_primary" class="block text-sm font-medium text-gray-700 mb-1">Couleur principale</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="color_primary" id="color_primary" value="{{ old('color_primary', '#059669') }}" class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" value="{{ old('color_primary', '#059669') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" id="color_primary_text" onchange="document.getElementById('color_primary').value = this.value">
                            </div>
                        </div>
                        <div>
                            <label for="color_secondary" class="block text-sm font-medium text-gray-700 mb-1">Couleur secondaire</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="color_secondary" id="color_secondary" value="{{ old('color_secondary', '#6B7280') }}" class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" value="{{ old('color_secondary', '#6B7280') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" id="color_secondary_text" onchange="document.getElementById('color_secondary').value = this.value">
                            </div>
                        </div>
                        <div>
                            <label for="logo_position" class="block text-sm font-medium text-gray-700 mb-1">Position du logo</label>
                            <select name="logo_position" id="logo_position" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="left" {{ old('logo_position') == 'left' ? 'selected' : '' }}>Gauche</option>
                                <option value="center" {{ old('logo_position') == 'center' ? 'selected' : '' }}>Centre</option>
                                <option value="right" {{ old('logo_position') == 'right' ? 'selected' : '' }}>Droite</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Options</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="show_logo" value="1" {{ old('show_logo', true) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Afficher le logo</span>
                        </label>
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="show_payment_info" value="1" {{ old('show_payment_info', true) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Afficher les infos de paiement</span>
                        </label>
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Modèle par défaut</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('document-templates.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Créer le modèle</button>
        </div>
    </form>
</div>

<script>
document.getElementById('color_primary').addEventListener('input', function() {
    document.getElementById('color_primary_text').value = this.value;
});
document.getElementById('color_secondary').addEventListener('input', function() {
    document.getElementById('color_secondary_text').value = this.value;
});
</script>
@endsection
