@extends('layouts.dashboard')

@section('title', 'Entrepôts')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Entrepôts</h1>
            <p class="mt-1 text-sm text-gray-500">Gérez vos entrepôts et lieux de stockage.</p>
        </div>
        <a href="{{ route('warehouses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvel entrepôt
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom, code..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="w-40">
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="is_active" id="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">Filtrer</button>
        </form>
    </div>

    <!-- Warehouses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($warehouses as $warehouse)
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $warehouse->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $warehouse->code }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($warehouse->is_default)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Par défaut</span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $warehouse->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $warehouse->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    @if($warehouse->address)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ Str::limit($warehouse->address, 30) }}
                        </p>
                    @endif
                    @if($warehouse->manager_name)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $warehouse->manager_name }}
                        </p>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $warehouse->stocks_count }}</p>
                        <p class="text-xs text-gray-500">produits en stock</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('warehouses.show', $warehouse) }}" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg" title="Voir">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="p-2 text-primary-600 hover:text-primary-900 hover:bg-primary-50 rounded-lg" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="mt-2 text-gray-500">Aucun entrepôt trouvé.</p>
                <a href="{{ route('warehouses.create') }}" class="mt-4 inline-flex items-center gap-2 text-primary-600 hover:text-primary-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Créer un entrepôt
                </a>
            </div>
        @endforelse
    </div>

    @if($warehouses->hasPages())
        <div class="mt-6">
            {{ $warehouses->links() }}
        </div>
    @endif
</div>
@endsection
