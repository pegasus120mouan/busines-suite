<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::orderBy('rate')->get();

        return view('tax-rates.index', compact('taxRates'));
    }

    public function create()
    {
        return view('tax-rates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_default']) {
            TaxRate::where('is_default', true)->update(['is_default' => false]);
        }

        TaxRate::create($validated);

        return redirect()->route('tax-rates.index')->with('success', 'Taux de TVA créé avec succès.');
    }

    public function edit(TaxRate $taxRate)
    {
        return view('tax-rates.edit', compact('taxRate'));
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_default'] && !$taxRate->is_default) {
            TaxRate::where('is_default', true)->update(['is_default' => false]);
        }

        $taxRate->update($validated);

        return redirect()->route('tax-rates.index')->with('success', 'Taux de TVA mis à jour.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return redirect()->route('tax-rates.index')->with('success', 'Taux de TVA supprimé.');
    }
}
