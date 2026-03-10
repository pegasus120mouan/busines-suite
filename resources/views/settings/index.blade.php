@extends('layouts.dashboard')

@section('title', 'Paramètres')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Paramètres</h1>
        <p class="mt-1 text-sm text-gray-500">Configurez les informations de votre entreprise.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2 text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Company Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de l'entreprise</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'entreprise *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $tenant->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $tenant->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea name="address" id="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('address', $tenant->address) }}</textarea>
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $tenant->settings['city'] ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $tenant->settings['postal_code'] ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                    <select name="country" id="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Sélectionner</option>
                        <option value="FR" {{ old('country', $tenant->settings['country'] ?? '') == 'FR' ? 'selected' : '' }}>France</option>
                        <option value="BE" {{ old('country', $tenant->settings['country'] ?? '') == 'BE' ? 'selected' : '' }}>Belgique</option>
                        <option value="CH" {{ old('country', $tenant->settings['country'] ?? '') == 'CH' ? 'selected' : '' }}>Suisse</option>
                        <option value="LU" {{ old('country', $tenant->settings['country'] ?? '') == 'LU' ? 'selected' : '' }}>Luxembourg</option>
                        <option value="CA" {{ old('country', $tenant->settings['country'] ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                        <option value="MA" {{ old('country', $tenant->settings['country'] ?? '') == 'MA' ? 'selected' : '' }}>Maroc</option>
                        <option value="TN" {{ old('country', $tenant->settings['country'] ?? '') == 'TN' ? 'selected' : '' }}>Tunisie</option>
                        <option value="SN" {{ old('country', $tenant->settings['country'] ?? '') == 'SN' ? 'selected' : '' }}>Sénégal</option>
                        <option value="CI" {{ old('country', $tenant->settings['country'] ?? '') == 'CI' ? 'selected' : '' }}>Côte d'Ivoire</option>
                    </select>
                </div>
                <div>
                    <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-1">N° TVA</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $tenant->settings['tax_number'] ?? '') }}" placeholder="FR12345678901" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">N° SIRET / Registre commerce</label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $tenant->settings['registration_number'] ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Logo -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Logo</h2>
            <div class="flex items-start gap-6">
                <div class="flex-shrink-0">
                    <div id="logo-preview-container">
                        @if($tenant->logo)
                            <img id="current-logo" src="{{ Storage::url($tenant->logo) }}" alt="Logo" class="w-32 h-32 object-contain border border-gray-200 rounded-lg bg-white">
                        @else
                            <div id="no-logo-placeholder" class="w-32 h-32 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <img id="logo-preview" src="#" alt="Aperçu" class="w-32 h-32 object-contain border-2 border-primary-500 rounded-lg bg-white hidden">
                    </div>
                </div>
                <div class="flex-1">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Télécharger un nouveau logo</label>
                    <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" onchange="previewLogo(this)">
                    <p class="mt-2 text-xs text-gray-500">PNG, JPG, GIF ou SVG. Max 2 Mo.</p>
                    <p id="preview-label" class="mt-2 text-xs text-primary-600 font-medium hidden">Aperçu du nouveau logo</p>
                    @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    
                    @if($tenant->logo)
                        <button type="button" onclick="document.getElementById('delete-logo-form').submit()" class="mt-3 text-sm text-red-600 hover:text-red-800">
                            Supprimer le logo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <script>
            function previewLogo(input) {
                const preview = document.getElementById('logo-preview');
                const previewLabel = document.getElementById('preview-label');
                const currentLogo = document.getElementById('current-logo');
                const noLogoPlaceholder = document.getElementById('no-logo-placeholder');
                
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        previewLabel.classList.remove('hidden');
                        
                        if (currentLogo) {
                            currentLogo.classList.add('hidden');
                        }
                        if (noLogoPlaceholder) {
                            noLogoPlaceholder.classList.add('hidden');
                        }
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

        <!-- Regional Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Paramètres régionaux</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Devise *</label>
                    <select name="currency" id="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="EUR" {{ old('currency', $tenant->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro (€)</option>
                        <option value="USD" {{ old('currency', $tenant->currency) == 'USD' ? 'selected' : '' }}>USD - Dollar US ($)</option>
                        <option value="GBP" {{ old('currency', $tenant->currency) == 'GBP' ? 'selected' : '' }}>GBP - Livre Sterling (£)</option>
                        <option value="CHF" {{ old('currency', $tenant->currency) == 'CHF' ? 'selected' : '' }}>CHF - Franc Suisse</option>
                        <option value="CAD" {{ old('currency', $tenant->currency) == 'CAD' ? 'selected' : '' }}>CAD - Dollar Canadien</option>
                        <option value="MAD" {{ old('currency', $tenant->currency) == 'MAD' ? 'selected' : '' }}>MAD - Dirham Marocain</option>
                        <option value="TND" {{ old('currency', $tenant->currency) == 'TND' ? 'selected' : '' }}>TND - Dinar Tunisien</option>
                        <option value="XOF" {{ old('currency', $tenant->currency) == 'XOF' ? 'selected' : '' }}>XOF - Franc CFA</option>
                    </select>
                </div>
                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Fuseau horaire *</label>
                    <select name="timezone" id="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="Europe/Paris" {{ old('timezone', $tenant->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                        <option value="Europe/Brussels" {{ old('timezone', $tenant->timezone) == 'Europe/Brussels' ? 'selected' : '' }}>Europe/Brussels</option>
                        <option value="Europe/Zurich" {{ old('timezone', $tenant->timezone) == 'Europe/Zurich' ? 'selected' : '' }}>Europe/Zurich</option>
                        <option value="Europe/London" {{ old('timezone', $tenant->timezone) == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                        <option value="America/Montreal" {{ old('timezone', $tenant->timezone) == 'America/Montreal' ? 'selected' : '' }}>America/Montreal</option>
                        <option value="Africa/Casablanca" {{ old('timezone', $tenant->timezone) == 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca</option>
                        <option value="Africa/Tunis" {{ old('timezone', $tenant->timezone) == 'Africa/Tunis' ? 'selected' : '' }}>Africa/Tunis</option>
                        <option value="Africa/Dakar" {{ old('timezone', $tenant->timezone) == 'Africa/Dakar' ? 'selected' : '' }}>Africa/Dakar</option>
                        <option value="Africa/Abidjan" {{ old('timezone', $tenant->timezone) == 'Africa/Abidjan' ? 'selected' : '' }}>Africa/Abidjan</option>
                    </select>
                </div>
                <div>
                    <label for="locale" class="block text-sm font-medium text-gray-700 mb-1">Langue *</label>
                    <select name="locale" id="locale" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="fr" {{ old('locale', $tenant->locale) == 'fr' ? 'selected' : '' }}>Français</option>
                        <option value="en" {{ old('locale', $tenant->locale) == 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bank Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Coordonnées bancaires</h2>
            <p class="text-sm text-gray-500 mb-4">Ces informations apparaîtront sur vos factures.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la banque</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $tenant->settings['bank_name'] ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="bank_iban" class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                    <input type="text" name="bank_iban" id="bank_iban" value="{{ old('bank_iban', $tenant->settings['bank_iban'] ?? '') }}" placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="bank_bic" class="block text-sm font-medium text-gray-700 mb-1">BIC/SWIFT</label>
                    <input type="text" name="bank_bic" id="bank_bic" value="{{ old('bank_bic', $tenant->settings['bank_bic'] ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Document Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Paramètres des documents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="invoice_prefix" class="block text-sm font-medium text-gray-700 mb-1">Préfixe des factures</label>
                    <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ old('invoice_prefix', $tenant->settings['invoice_prefix'] ?? 'FAC-') }}" placeholder="FAC-" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="quote_prefix" class="block text-sm font-medium text-gray-700 mb-1">Préfixe des devis</label>
                    <input type="text" name="quote_prefix" id="quote_prefix" value="{{ old('quote_prefix', $tenant->settings['quote_prefix'] ?? 'DEV-') }}" placeholder="DEV-" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label for="invoice_footer" class="block text-sm font-medium text-gray-700 mb-1">Pied de page des factures</label>
                    <textarea name="invoice_footer" id="invoice_footer" rows="2" placeholder="Conditions de paiement, mentions légales..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('invoice_footer', $tenant->settings['invoice_footer'] ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label for="quote_footer" class="block text-sm font-medium text-gray-700 mb-1">Pied de page des devis</label>
                    <textarea name="quote_footer" id="quote_footer" rows="2" placeholder="Conditions de validité, mentions légales..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('quote_footer', $tenant->settings['quote_footer'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

@if($tenant->logo)
    <form id="delete-logo-form" method="POST" action="{{ route('settings.delete-logo') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif
@endsection
