@extends('layouts.dashboard')

@section('title', 'Nouveau département')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.departments.index') }}" class="p-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            @if(request('parent_id'))
                Nouveau sous-département
            @else
                Nouveau département
            @endif
        </h1>
    </div>

    <form action="{{ route('hr.departments.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <p class="mt-1 text-xs text-gray-500">Le code sera généré automatiquement</p>
        </div>
        @if(request('direction_id'))
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
            <select name="direction_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 bg-gray-50">
                @foreach($directions as $dir)
                    @if($dir->id == request('direction_id'))
                        <option value="{{ $dir->id }}" selected>{{ $dir->name }}</option>
                    @endif
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">La direction est pré-sélectionnée</p>
        </div>
        @else
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
            <select name="direction_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Sélectionner une direction</option>
                @foreach($directions as $dir)
                    <option value="{{ $dir->id }}" {{ old('direction_id') == $dir->id ? 'selected' : '' }}>{{ $dir->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
        </div>
        @if(request('parent_id'))
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Département parent</label>
            <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 bg-gray-50" readonly>
                @foreach($departments as $dept)
                    @if($dept->id == request('parent_id'))
                        <option value="{{ $dept->id }}" selected>{{ $dept->name }}</option>
                    @endif
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Le département parent est pré-sélectionné</p>
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
            <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" class="w-20 h-10 border border-gray-300 rounded-lg">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="is_active" class="text-sm text-gray-700">Actif</label>
        </div>
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('hr.departments.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Créer</button>
        </div>
    </form>
</div>
@endsection
