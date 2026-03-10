<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy($request->sort ?? 'company_name', $request->direction ?? 'asc');

        $suppliers = $query->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:255', Rule::unique('suppliers')->where('tenant_id', auth()->user()->tenant_id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'payment_terms' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['products', 'purchaseOrders' => fn ($q) => $q->latest()->limit(10)]);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:255', Rule::unique('suppliers')->where('tenant_id', auth()->user()->tenant_id)->ignore($supplier->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'payment_terms' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Fournisseur mis à jour.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->exists() || $supplier->purchaseOrders()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce fournisseur.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Fournisseur supprimé.');
    }
}
