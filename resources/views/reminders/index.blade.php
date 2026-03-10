@extends('layouts.dashboard')

@section('title', 'Relances')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des relances</h1>
            <p class="mt-1 text-sm text-gray-500">Suivi et programmation des relances pour impayés</p>
        </div>
        <a href="{{ route('reminders.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle relance
        </a>
    </div>

    @if($overdueInvoices->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-amber-800">{{ $overdueInvoices->count() }} facture(s) en retard sans relance programmée</h3>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($overdueInvoices->take(5) as $invoice)
                            <a href="{{ route('reminders.create', ['invoice_id' => $invoice->id]) }}" class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded hover:bg-amber-200">
                                {{ $invoice->invoice_number }}
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </a>
                        @endforeach
                        @if($overdueInvoices->count() > 5)
                            <span class="text-xs text-amber-700">+{{ $overdueInvoices->count() - 5 }} autres</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
            <select name="level" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Tous les niveaux</option>
                <option value="1" {{ request('level') == '1' ? 'selected' : '' }}>1ère relance</option>
                <option value="2" {{ request('level') == '2' ? 'selected' : '' }}>2ème relance</option>
                <option value="3" {{ request('level') == '3' ? 'selected' : '' }}>Mise en demeure</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">Filtrer</button>
        </form>
    </div>

    <!-- Reminders Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date prévue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reminders as $reminder)
                        @php
                            $levelColors = [
                                1 => 'bg-blue-100 text-blue-800',
                                2 => 'bg-amber-100 text-amber-800',
                                3 => 'bg-red-100 text-red-800',
                            ];
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'sent' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'sent' => 'Envoyée',
                                'failed' => 'Échouée',
                                'cancelled' => 'Annulée',
                            ];
                            $typeLabels = [
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'letter' => 'Courrier',
                                'phone' => 'Téléphone',
                                'manual' => 'Manuel',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('invoices.show', $reminder->invoice) }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                                    {{ $reminder->invoice->invoice_number }}
                                </a>
                                <p class="text-xs text-gray-500">{{ number_format($reminder->invoice->balance_due, 0, ',', ' ') }} {{ $currencySymbol }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $reminder->invoice->customer?->display_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $levelColors[$reminder->level] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ \App\Models\Reminder::getLevelLabel($reminder->level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $typeLabels[$reminder->type] ?? $reminder->type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $reminder->scheduled_date->format('d/m/Y') }}
                                @if($reminder->sent_at)
                                    <br><span class="text-xs text-green-600">Envoyée le {{ $reminder->sent_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$reminder->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$reminder->status] ?? $reminder->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($reminder->status === 'pending')
                                    <form method="POST" action="{{ route('reminders.mark-sent', $reminder) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-500 mr-3">Marquer envoyée</button>
                                    </form>
                                    <form method="POST" action="{{ route('reminders.cancel', $reminder) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-500">Annuler</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Aucune relance programmée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reminders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reminders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
