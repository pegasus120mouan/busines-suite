@extends('layouts.dashboard')

@section('title', $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('users.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- User Info -->
        <div class="md:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                    <dd class="mt-1">
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $role->name === 'manager' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $role->name === 'accountant' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $role->name === 'sales' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ !in_array($role->name, ['admin', 'manager', 'accountant', 'sales']) ? 'bg-gray-100 text-gray-800' : '' }}
                            ">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dernière connexion</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Permissions -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h2>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($user->getAllPermissions() as $permission)
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-700">{{ str_replace('_', ' ', ucfirst($permission->name)) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune permission assignée.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
