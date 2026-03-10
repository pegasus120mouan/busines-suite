@extends('layouts.dashboard')

@section('title', 'Journal d\'audit')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Journal d'audit</h1>
            <p class="mt-1 text-sm text-gray-500">Historique des modifications dans le système</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="event" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Tous les événements</option>
                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Création</option>
                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Modification</option>
                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Suppression</option>
                <option value="restored" {{ request('event') == 'restored' ? 'selected' : '' }}>Restauration</option>
            </select>
            <select name="user_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="model" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Tous les types</option>
                @foreach($modelTypes as $key => $label)
                    <option value="{{ $key }}" {{ request('model') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Date début">
            <div class="flex gap-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Date fin">
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Filtrer</button>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élément</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        @php
                            $eventColors = [
                                'created' => 'bg-green-100 text-green-800',
                                'updated' => 'bg-blue-100 text-blue-800',
                                'deleted' => 'bg-red-100 text-red-800',
                                'restored' => 'bg-purple-100 text-purple-800',
                            ];
                            $eventLabels = [
                                'created' => 'Création',
                                'updated' => 'Modification',
                                'deleted' => 'Suppression',
                                'restored' => 'Restauration',
                            ];
                            $modelName = class_basename($log->auditable_type);
                            $modelLabels = [
                                'Customer' => 'Client',
                                'Invoice' => 'Facture',
                                'Quote' => 'Devis',
                                'Product' => 'Produit',
                                'Expense' => 'Dépense',
                                'Payment' => 'Paiement',
                                'Supplier' => 'Fournisseur',
                                'User' => 'Utilisateur',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-semibold text-primary-700">{{ strtoupper(substr($log->user?->name ?? '?', 0, 2)) }}</span>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ $log->user?->name ?? 'Système' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $eventColors[$log->event] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $eventLabels[$log->event] ?? $log->event }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $modelLabels[$modelName] ?? $modelName }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                #{{ $log->auditable_id }}
                                @if($log->auditable)
                                    @if(method_exists($log->auditable, 'getDisplayNameAttribute'))
                                        - {{ $log->auditable->display_name }}
                                    @elseif(isset($log->auditable->name))
                                        - {{ $log->auditable->name }}
                                    @elseif(isset($log->auditable->invoice_number))
                                        - {{ $log->auditable->invoice_number }}
                                    @elseif(isset($log->auditable->quote_number))
                                        - {{ $log->auditable->quote_number }}
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" onclick="showDetails({{ $log->id }})" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                    Voir détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucun enregistrement trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Détails de la modification</h3>
            <button type="button" onclick="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]" id="modal-content">
            <!-- Content loaded dynamically -->
        </div>
    </div>
</div>

<script>
const logsData = @json($logs->items());

function showDetails(logId) {
    const log = logsData.find(l => l.id === logId);
    if (!log) return;

    let html = '<div class="space-y-4">';
    
    if (log.old_values && Object.keys(log.old_values).length > 0) {
        html += '<div><h4 class="text-sm font-medium text-gray-700 mb-2">Anciennes valeurs</h4>';
        html += '<div class="bg-red-50 rounded-lg p-3 text-sm">';
        for (const [key, value] of Object.entries(log.old_values)) {
            html += `<div class="flex justify-between py-1 border-b border-red-100 last:border-0">
                <span class="font-medium text-gray-700">${key}</span>
                <span class="text-red-700">${formatValue(value)}</span>
            </div>`;
        }
        html += '</div></div>';
    }

    if (log.new_values && Object.keys(log.new_values).length > 0) {
        html += '<div><h4 class="text-sm font-medium text-gray-700 mb-2">Nouvelles valeurs</h4>';
        html += '<div class="bg-green-50 rounded-lg p-3 text-sm">';
        for (const [key, value] of Object.entries(log.new_values)) {
            html += `<div class="flex justify-between py-1 border-b border-green-100 last:border-0">
                <span class="font-medium text-gray-700">${key}</span>
                <span class="text-green-700">${formatValue(value)}</span>
            </div>`;
        }
        html += '</div></div>';
    }

    html += `<div class="text-xs text-gray-500 pt-4 border-t border-gray-200">
        <p>IP: ${log.ip_address || 'N/A'}</p>
        <p>URL: ${log.url || 'N/A'}</p>
    </div>`;

    html += '</div>';

    document.getElementById('modal-content').innerHTML = html;
    document.getElementById('details-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('details-modal').classList.add('hidden');
}

function formatValue(value) {
    if (value === null) return '<em>null</em>';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
}

document.getElementById('details-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
