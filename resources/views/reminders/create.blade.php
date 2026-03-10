@extends('layouts.dashboard')

@section('title', 'Nouvelle relance')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('reminders.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Programmer une relance</h1>
            <p class="mt-1 text-sm text-gray-500">Créer une nouvelle relance pour une facture impayée</p>
        </div>
    </div>

    <form method="POST" action="{{ route('reminders.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de la relance</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-1">Facture *</label>
                    <select name="invoice_id" id="invoice_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required onchange="updateDefaults()">
                        <option value="">Sélectionner une facture</option>
                        @foreach($invoices as $inv)
                            <option value="{{ $inv->id }}" {{ ($invoice && $invoice->id == $inv->id) || old('invoice_id') == $inv->id ? 'selected' : '' }}
                                data-customer="{{ $inv->customer?->display_name }}"
                                data-amount="{{ number_format($inv->balance_due, 0, ',', ' ') }}"
                                data-due="{{ $inv->due_date->format('d/m/Y') }}">
                                {{ $inv->invoice_number }} - {{ $inv->customer?->display_name }} ({{ number_format($inv->balance_due, 0, ',', ' ') }} {{ $currencySymbol }})
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Niveau de relance *</label>
                    <select name="level" id="level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required onchange="updateDefaults()">
                        <option value="1" {{ old('level', $level) == 1 ? 'selected' : '' }}>1ère relance (rappel amical)</option>
                        <option value="2" {{ old('level', $level) == 2 ? 'selected' : '' }}>2ème relance (rappel ferme)</option>
                        <option value="3" {{ old('level', $level) == 3 ? 'selected' : '' }}>3ème relance (mise en demeure)</option>
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type d'envoi *</label>
                    <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="letter" {{ old('type') == 'letter' ? 'selected' : '' }}>Courrier</option>
                        <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Téléphone</option>
                        <option value="manual" {{ old('type') == 'manual' ? 'selected' : '' }}>Manuel</option>
                    </select>
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Date prévue *</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>

                <div class="md:col-span-2">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Objet</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Objet de la relance">
                </div>

                <div class="md:col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" id="message" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Contenu du message de relance">{{ old('message') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Notes visibles uniquement en interne">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('reminders.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Programmer la relance</button>
        </div>
    </form>
</div>

<script>
const defaultSubjects = {
    1: "Rappel de paiement - Facture",
    2: "Second rappel - Facture en attente",
    3: "Mise en demeure - Facture"
};

const defaultMessages = {
    1: "Bonjour,\n\nNous vous rappelons que votre facture est arrivée à échéance.\n\nNous vous remercions de bien vouloir procéder au règlement dans les meilleurs délais.\n\nCordialement",
    2: "Bonjour,\n\nMalgré notre précédent rappel, nous constatons que votre facture reste impayée.\n\nNous vous prions de régulariser cette situation dans les plus brefs délais.\n\nCordialement",
    3: "Bonjour,\n\nLa présente constitue une mise en demeure de payer votre facture.\n\nSans règlement de votre part sous 8 jours, nous serons contraints d'engager des poursuites.\n\nCordialement"
};

function updateDefaults() {
    const level = document.getElementById('level').value;
    const subjectField = document.getElementById('subject');
    const messageField = document.getElementById('message');
    
    if (!subjectField.value || subjectField.value.startsWith('Rappel') || subjectField.value.startsWith('Second') || subjectField.value.startsWith('Mise en demeure')) {
        subjectField.value = defaultSubjects[level] || '';
    }
    
    if (!messageField.value || messageField.value.startsWith('Bonjour')) {
        messageField.value = defaultMessages[level] || '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('subject').value) {
        updateDefaults();
    }
});
</script>
@endsection
