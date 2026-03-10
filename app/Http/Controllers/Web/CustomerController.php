<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc');

        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:individual,company'],
            'company_name' => ['nullable', 'required_if:type,company', 'string', 'max:255'],
            'first_name' => ['nullable', 'required_if:type,individual', 'string', 'max:100'],
            'last_name' => ['nullable', 'required_if:type,individual', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers')->where('tenant_id', auth()->user()->tenant_id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string', 'max:500'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_postal_code' => ['nullable', 'string', 'max:20'],
            'billing_country' => ['nullable', 'string', 'size:2'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'size:2'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Client créé avec succès.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['invoices' => fn ($q) => $q->latest()->limit(10), 'quotes' => fn ($q) => $q->latest()->limit(10)]);
        
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:individual,company'],
            'company_name' => ['nullable', 'required_if:type,company', 'string', 'max:255'],
            'first_name' => ['nullable', 'required_if:type,individual', 'string', 'max:100'],
            'last_name' => ['nullable', 'required_if:type,individual', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers')->where('tenant_id', auth()->user()->tenant_id)->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string', 'max:500'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_postal_code' => ['nullable', 'string', 'max:20'],
            'billing_country' => ['nullable', 'string', 'size:2'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'size:2'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists() || $customer->quotes()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce client car il a des factures ou devis associés.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Client supprimé avec succès.');
    }
}
