@extends('layouts.dashboard')

@section('title', 'Nouvelle dépense')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux dépenses
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Nouvelle dépense</h1>
    </div>

    <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror" required>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Montant HT *</label>
                    <div class="relative">
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0.01" class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <span class="absolute right-3 top-2.5 text-gray-500">{{ $currencySymbol }}</span>
                    </div>
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-1">TVA</label>
                    <div class="relative">
                        <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0" class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <span class="absolute right-3 top-2.5 text-gray-500">{{ $currencySymbol }}</span>
                    </div>
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                    <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Non spécifié</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Carte bancaire</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement</option>
                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Chèque</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Référence / N° facture</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_billable" value="1" {{ old('is_billable') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Dépense refacturable au client</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('expenses.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
