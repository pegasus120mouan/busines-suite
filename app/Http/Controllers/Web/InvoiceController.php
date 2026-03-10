<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()
            ->with('customer')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc');

        $invoices = $query->paginate(15)->withQueryString();
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();

        return view('invoices.index', compact('invoices', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedCustomer = $request->customer ? Customer::find($request->customer) : null;

        return view('invoices.create', compact('customers', 'products', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $invoice = DB::transaction(function () use ($validated, $request) {
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Facture créée avec succès.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'payments', 'user']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if (!in_array($invoice->status, ['draft'])) {
            return back()->with('error', 'Cette facture ne peut plus être modifiée.');
        }

        $invoice->load('items');
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'customers', 'products'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if (!in_array($invoice->status, ['draft'])) {
            return back()->with('error', 'Cette facture ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Facture mise à jour.');
    }

    public function destroy(Invoice $invoice)
    {
        if (!in_array($invoice->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Cette facture ne peut pas être supprimée.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Facture supprimée.');
    }

    public function send(Invoice $invoice)
    {
        if ($invoice->status === 'draft') {
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return back()->with('success', 'Facture marquée comme envoyée.');
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $invoice->balance_due],
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'in:cash,bank_transfer,check,credit_card,paypal,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Payment::create([
            'tenant_id' => auth()->user()->tenant_id,
            'invoice_id' => $invoice->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'method' => $validated['method'],
            'reference' => $validated['reference'],
            'notes' => $validated['notes'],
            'status' => 'completed',
        ]);

        return back()->with('success', 'Paiement enregistré.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'tenant']);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download("facture-{$invoice->invoice_number}.pdf");
    }
}
