@extends('layouts.dashboard')

@section('title', 'Postes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Postes</h1>
            <p class="text-gray-600">Gérez les postes et les salaires de base</p>
        </div>
        <a href="{{ route('hr.postes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau poste
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Total des postes</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Postes actifs</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un poste..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="w-48">
                <select name="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Tous les départements</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Filtrer</button>
            @if(request()->hasAny(['search', 'department_id']))
                <a href="{{ route('hr.postes.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poste</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Département</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salaire de base</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employés</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($postes as $poste)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $poste->name }}</p>
                                @if($poste->code)
                                    <p class="text-sm text-gray-500">{{ $poste->code }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($poste->department)
                                <span class="text-gray-700">{{ $poste->department->name }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ number_format($poste->salaire_base, 0, ',', ' ') }} XOF</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $poste->employees->count() }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $poste->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $poste->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.postes.edit', $poste) }}" class="p-2 text-gray-400 hover:text-primary-600" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($poste->employees->count() === 0)
                                <button type="button" onclick="openDeleteModal('{{ $poste->id }}', '{{ $poste->name }}')" class="p-2 text-gray-400 hover:text-red-600" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucun poste trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($postes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $postes->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
            <div style="background-color: #dc2626;" class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 style="color: white;" class="font-medium">Confirmer la suppression</h3>
                </div>
                <button type="button" onclick="closeDeleteModal()" style="color: white;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #fee2e2;">
                        <svg class="w-8 h-8" style="color: #ef4444;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-center text-gray-700 mb-4">Êtes-vous sûr de vouloir supprimer ce poste ?</p>
                <div class="rounded-lg p-3 mb-4" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
                    <p class="text-sm text-gray-600"><strong class="text-gray-800">Poste :</strong> <span id="posteName"></span></p>
                </div>
                <div class="rounded-lg p-3 flex items-start gap-2" style="background-color: #fffbeb; border: 1px solid #fcd34d;">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #f59e0b;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <p class="text-sm" style="color: #b45309;">Cette action est irréversible.</p>
                </div>
            </div>
            <div class="px-6 py-4 flex justify-end gap-3" style="background-color: #f9fafb; border-top: 1px solid #e5e7eb;">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background-color: #dc2626; color: white;" class="px-4 py-2 text-sm font-medium rounded-lg hover:opacity-90 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" style="background-color: white; color: #374151; border: 1px solid #d1d5db;" class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id, name) {
        document.getElementById('posteName').textContent = name;
        document.getElementById('deleteForm').action = '{{ url("hr/postes") }}/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endsection
