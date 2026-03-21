@extends('layouts.dashboard')

@section('title', 'Départements')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Départements</h1>
            <p class="text-gray-600">Gérez la structure organisationnelle</p>
        </div>
        <a href="{{ route('hr.departments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau département
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Département</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Direction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sous-dép.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employés</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($departments as $department)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($department->parent_id)
                                    <div class="w-6 flex justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $department->color ?? '#3B82F6' }}20">
                                    <svg class="w-5 h-5" style="color: {{ $department->color ?? '#3B82F6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $department->name }}</p>
                                    @if($department->parent)
                                        <p class="text-sm text-gray-500">
                                            <span class="text-gray-400">↳</span> {{ $department->parent->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($department->direction)
                                <a href="{{ route('hr.directions.show', $department->direction) }}" class="text-primary-600 hover:text-primary-700">
                                    {{ $department->direction->name }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($department->children->count() > 0)
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                    {{ $department->children->count() }} sous-dép.
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $department->employees->count() }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $department->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.departments.create', ['parent_id' => $department->id]) }}" class="p-2 text-gray-400 hover:text-green-600" title="Créer un sous-département">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </a>
                                <a href="{{ route('hr.departments.edit', $department) }}" class="p-2 text-gray-400 hover:text-primary-600" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($department->employees->count() === 0 && $department->children->count() === 0)
                                <button type="button" onclick="openDeleteModal('{{ $department->id }}', '{{ $department->name }}')" class="p-2 text-gray-400 hover:text-red-600" title="Supprimer">
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucun département</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($departments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $departments->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/40 transition-opacity" onclick="closeDeleteModal()"></div>

    <!-- Contenu du modal centré -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
            <!-- Header rouge -->
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
            
            <!-- Corps -->
            <div class="p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #fee2e2;">
                        <svg class="w-8 h-8" style="color: #ef4444;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-center text-gray-700 mb-4">
                    Êtes-vous sûr de vouloir supprimer ce département ?
                </p>
                
                <!-- Info box -->
                <div class="rounded-lg p-3 mb-4" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
                    <p class="text-sm text-gray-600">
                        <strong class="text-gray-800">Département :</strong> <span id="departmentName"></span>
                    </p>
                </div>
                
                <!-- Warning -->
                <div class="rounded-lg p-3 flex items-start gap-2" style="background-color: #fffbeb; border: 1px solid #fcd34d;">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #f59e0b;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <p class="text-sm" style="color: #b45309;">Cette action est irréversible.</p>
                </div>
            </div>
            
            <!-- Footer -->
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
        document.getElementById('departmentName').textContent = name;
        document.getElementById('deleteForm').action = '{{ url("hr/departments") }}/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endsection
