@extends('layouts.dashboard')

@section('title', 'Prospects')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Prospects</h1>
            <p class="mt-1 text-sm text-gray-500">Suivez vos prospects et leurs coordonnées.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openHsmsSettingsModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Paramètres SMS
                @if($hsmsConfigured)
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                @else
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                @endif
            </button>
            <button type="button" onclick="openProspectModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau prospect
            </button>
        </div>
    </div>

    @unless($hsmsConfigured)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-sm text-amber-800">
                Configurez l’intégrateur <strong>HSMS</strong> pour pouvoir envoyer des SMS aux prospects.
            </div>
            <button type="button" onclick="openHsmsSettingsModal()" class="shrink-0 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700">
                Configurer maintenant
            </button>
        </div>
    @endunless

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un prospect..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Filtrer
            </button>
            @if(request('search'))
                <a href="{{ route('prospects.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prospect</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS envoyés</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ajouté le</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prospects as $prospect)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-primary-700">{{ $prospect->initials }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $prospect->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $prospect->first_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospect->contact }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($prospect->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $prospect->whatsapp) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-green-600 hover:text-green-700">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        {{ $prospect->whatsapp }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col items-start gap-0.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ ($prospect->sms_sent_count ?? 0) > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        {{ $prospect->sms_sent_count ?? 0 }}
                                    </span>
                                    @if($prospect->last_sms_sent_at)
                                        <span class="text-[11px] text-gray-400">{{ $prospect->last_sms_sent_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $prospect->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                        onclick='openSendSmsModal(@json($prospect))'
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Envoyer un SMS">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        <span class="hidden lg:inline text-xs font-semibold uppercase tracking-wide">SMS</span>
                                    </button>
                                    <button type="button"
                                        onclick='openProspectModal(@json($prospect))'
                                        class="text-primary-600 hover:text-primary-800 p-1.5" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        onclick="openDeleteProspectModal({{ $prospect->id }}, {{ json_encode($prospect->full_name) }})"
                                        class="text-red-500 hover:text-red-700 p-1.5" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500">Aucun prospect pour le moment</p>
                                    <button type="button" onclick="openProspectModal()" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                        Ajouter un prospect
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($prospects->hasPages())
        <div class="mt-4">
            {{ $prospects->links() }}
        </div>
    @endif
</div>

{{-- Modal HSMS settings --}}
<div id="hsmsSettingsModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('hsmsSettingsModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="{{ route('prospects.hsms-settings') }}">
                @csrf
                @method('PUT')
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Paramètres de l’intégrateur</h3>
                                <p class="mt-1 text-sm text-gray-500">Identifiants API HSMS pour l’envoi de SMS.</p>
                            </div>
                        </div>
                        <a href="https://hsms.ci/doc-api/" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                            Documentation
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Client ID</label>
                            <input type="text" name="client_id" value="{{ old('client_id', $hsmsSettings['client_id'] ?? '') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                                placeholder="Votre Client ID HSMS">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Client Secret</label>
                            <div class="relative">
                                <input type="password" name="client_secret" id="hsms_client_secret" value="{{ old('client_secret', $hsmsSettings['client_secret'] ?? '') }}" required
                                    class="w-full px-4 py-2.5 pr-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                                    placeholder="Votre Client Secret">
                                <button type="button" onclick="toggleSecretVisibility('hsms_client_secret', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg data-eye-on class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg data-eye-off class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Token API</label>
                            <div class="relative">
                                <input type="password" name="token" id="hsms_token" value="{{ old('token', $hsmsSettings['token'] ?? '') }}" required
                                    class="w-full px-4 py-2.5 pr-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                                    placeholder="Bearer token HSMS">
                                <button type="button" onclick="toggleSecretVisibility('hsms_token', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg data-eye-on class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg data-eye-off class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400">Obtenez le token via <code class="bg-gray-100 px-1 rounded">POST /api/token/</code> (email + mot de passe HSMS).</p>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-sm font-medium text-gray-900 mb-3">Message automatique</p>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom affiché (entreprise)</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $hsmsSettings['company_name'] ?? 'OVL Delivery Services') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="OVL Delivery Services">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact (dans le SMS)</label>
                                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $hsmsSettings['contact_phone'] ?? '0787703000') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="0787703000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp (dans le SMS)</label>
                                    <input type="text" name="whatsapp_phone" value="{{ old('whatsapp_phone', $hsmsSettings['whatsapp_phone'] ?? '084828385') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="084828385">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Modèle du message</label>
                                <textarea name="message_template" rows="3" maxlength="918"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                    placeholder="Besoin d'un service de livraison...">{{ old('message_template', $hsmsSettings['message_template'] ?? $smsTemplate) }}</textarea>
                                <p class="mt-1.5 text-xs text-gray-400">
                                    Variables : <code class="bg-gray-100 px-1 rounded">{entreprise}</code>
                                    <code class="bg-gray-100 px-1 rounded">{contact}</code>
                                    <code class="bg-gray-100 px-1 rounded">{whatsapp}</code>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('hsmsSettingsModal').classList.add('hidden')" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal send SMS --}}
<div id="sendSmsModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('sendSmsModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="sendSmsForm" method="POST">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Envoyer un SMS</h3>
                            <p class="mt-1 text-sm text-gray-500">À <span id="smsProspectName" class="font-medium text-gray-800"></span></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="sms_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Numéro</label>
                            <input type="text" name="phone" id="sms_phone" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="0700000000">
                            <p class="mt-1 text-xs text-gray-400">Format : 0787703000 → envoyé en 2250787703000.</p>
                        </div>
                        <div>
                            <label for="sms_message" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Message automatique
                                <span class="text-gray-400 font-normal">(personnalisé)</span>
                            </label>
                            <textarea name="message" id="sms_message" rows="5" required maxlength="918"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
                                oninput="updateSmsCounter()"></textarea>
                            <div class="mt-1 flex justify-between text-xs text-gray-400">
                                <span>Généré automatiquement pour ce prospect</span>
                                <span><span id="smsCharCount">0</span>/918</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('sendSmsModal').classList.add('hidden')" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer le SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal create / edit --}}
<div id="prospectModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeProspectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="prospectForm" method="POST" action="{{ route('prospects.store') }}">
                @csrf
                <input type="hidden" name="_method" id="prospectMethod" value="POST">

                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 id="prospectModalTitle" class="text-lg font-semibold text-gray-900">Nouveau prospect</h3>
                            <p class="mt-1 text-sm text-gray-500">Renseignez les informations du prospect.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="last_name" required maxlength="100"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Kouassi">
                            </div>
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">Prénoms <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="first_name" required maxlength="100"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Jean Baptiste">
                            </div>
                        </div>

                        <div>
                            <label for="contact" class="block text-sm font-medium text-gray-700 mb-1.5">Contact <span class="text-red-500">*</span></label>
                            <input type="text" name="contact" id="contact" required maxlength="100"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Téléphone ou email">
                        </div>

                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Numéro WhatsApp
                                <span class="text-gray-400 font-normal">(facultatif)</span>
                            </label>
                            <input type="text" name="whatsapp" id="whatsapp" maxlength="50"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="+225 07 00 00 00 00">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeProspectModal()" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" id="prospectSubmitBtn" class="px-4 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal delete --}}
<div id="deleteProspectModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('deleteProspectModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Supprimer ce prospect</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Voulez-vous vraiment supprimer <span id="deleteProspectName" class="font-medium text-gray-900"></span> ?
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('deleteProspectModal').classList.add('hidden')" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50">
                    Annuler
                </button>
                <form id="deleteProspectForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const prospectStoreUrl = @json(route('prospects.store'));
const prospectBaseUrl = @json(url('prospects'));
const hsmsConfigured = @json($hsmsConfigured);
const smsTemplate = @json($smsTemplate);
const smsCompany = @json($hsmsSettings['company_name'] ?? 'OVL Delivery Services');
const smsContact = @json($hsmsSettings['contact_phone'] ?? '0787703000');
const smsWhatsapp = @json($hsmsSettings['whatsapp_phone'] ?? '084828385');

function buildPersonalizedSms(prospect) {
    return smsTemplate
        .replaceAll('{prenom}', prospect.first_name || prospect.last_name || '')
        .replaceAll('{nom}', prospect.last_name || '')
        .replaceAll('{nom_complet}', ((prospect.last_name || '') + ' ' + (prospect.first_name || '')).trim())
        .replaceAll('{entreprise}', smsCompany)
        .replaceAll('{contact}', smsContact)
        .replaceAll('{whatsapp}', smsWhatsapp);
}

function openProspectModal(prospect = null) {
    const form = document.getElementById('prospectForm');
    const method = document.getElementById('prospectMethod');
    const title = document.getElementById('prospectModalTitle');
    const submitBtn = document.getElementById('prospectSubmitBtn');

    if (prospect) {
        form.action = prospectBaseUrl + '/' + prospect.id;
        method.value = 'PUT';
        title.textContent = 'Modifier le prospect';
        submitBtn.textContent = 'Mettre à jour';
        document.getElementById('last_name').value = prospect.last_name || '';
        document.getElementById('first_name').value = prospect.first_name || '';
        document.getElementById('contact').value = prospect.contact || '';
        document.getElementById('whatsapp').value = prospect.whatsapp || '';
    } else {
        form.action = prospectStoreUrl;
        method.value = 'POST';
        title.textContent = 'Nouveau prospect';
        submitBtn.textContent = 'Enregistrer';
        form.reset();
    }

    document.getElementById('prospectModal').classList.remove('hidden');
}

function closeProspectModal() {
    document.getElementById('prospectModal').classList.add('hidden');
}

function openDeleteProspectModal(id, name) {
    document.getElementById('deleteProspectName').textContent = name;
    document.getElementById('deleteProspectForm').action = prospectBaseUrl + '/' + id;
    document.getElementById('deleteProspectModal').classList.remove('hidden');
}

function openHsmsSettingsModal() {
    document.getElementById('hsmsSettingsModal').classList.remove('hidden');
}

function openSendSmsModal(prospect) {
    if (!hsmsConfigured) {
        openHsmsSettingsModal();
        return;
    }

    const phone = prospect.whatsapp || prospect.contact || '';
    document.getElementById('smsProspectName').textContent = (prospect.last_name + ' ' + prospect.first_name).trim();
    document.getElementById('sms_phone').value = phone;
    document.getElementById('sms_message').value = buildPersonalizedSms(prospect);
    document.getElementById('sendSmsForm').action = prospectBaseUrl + '/' + prospect.id + '/send-sms';
    updateSmsCounter();
    document.getElementById('sendSmsModal').classList.remove('hidden');
}

function updateSmsCounter() {
    document.getElementById('smsCharCount').textContent = (document.getElementById('sms_message').value || '').length;
}

function toggleSecretVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const eyeOn = button.querySelector('[data-eye-on]');
    const eyeOff = button.querySelector('[data-eye-off]');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    eyeOn.classList.toggle('hidden', isHidden);
    eyeOff.classList.toggle('hidden', !isHidden);
}

@if($errors->hasAny(['client_id', 'client_secret', 'token']))
    openHsmsSettingsModal();
@elseif($errors->any())
    openProspectModal();
@endif
</script>
@endsection
