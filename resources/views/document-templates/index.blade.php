@extends('layouts.dashboard')

@section('title', 'Modèles de documents')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modèles de documents</h1>
            <p class="mt-1 text-sm text-gray-500">Personnalisez vos factures, devis et relances</p>
        </div>
        <a href="{{ route('document-templates.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau modèle
        </a>
    </div>

    @php
        $typeLabels = [
            'invoice' => 'Factures',
            'quote' => 'Devis',
            'reminder' => 'Relances',
        ];
        $typeIcons = [
            'invoice' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'quote' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'reminder' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
        ];
    @endphp

    @foreach(['invoice', 'quote', 'reminder'] as $type)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $typeIcons[$type] !!}
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $typeLabels[$type] }}</h2>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($templates[$type] ?? [] as $template)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-4 h-4 rounded" style="background-color: {{ $template->color_primary }}"></div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ $template->name }}</span>
                                    @if($template->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Par défaut</span>
                                    @endif
                                    @if(!$template->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Inactif</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">Logo: {{ $template->logo_position }} | Infos paiement: {{ $template->show_payment_info ? 'Oui' : 'Non' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$template->is_default)
                                <form method="POST" action="{{ route('document-templates.set-default', $template) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-400 hover:text-green-600 rounded-lg hover:bg-gray-100" title="Définir par défaut">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('document-templates.edit', $template) }}" class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-gray-100" title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(!$template->is_default)
                                <form method="POST" action="{{ route('document-templates.destroy', $template) }}" class="inline" onsubmit="return confirm('Supprimer ce modèle ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>Aucun modèle pour ce type de document</p>
                        <a href="{{ route('document-templates.create') }}?type={{ $type }}" class="mt-2 inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer un modèle
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
