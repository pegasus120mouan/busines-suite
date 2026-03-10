<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::query()
            ->with('customer')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc');

        $quotes = $query->paginate(15)->withQueryString();
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();

        return view('quotes.index', compact('quotes', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedCustomer = $request->customer ? Customer::find($request->customer) : null;

        return view('quotes.create', compact('customers', 'products', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'quote_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:quote_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $quote = DB::transaction(function () use ($validated) {
            $quote = Quote::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'quote_date' => $validated['quote_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $index => $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'sort_order' => $index,
                ]);
            }

            return $quote;
        });

        return redirect()->route('quotes.show', $quote)->with('success', 'Devis créé avec succès.');
    }

    public function show(Quote $quote)
    {
        $quote->load(['customer', 'items.product', 'user', 'invoice']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        if (!in_array($quote->status, ['draft', 'sent'])) {
            return back()->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $quote->load('items');
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('quotes.edit', compact('quote', 'customers', 'products'));
    }

    public function update(Request $request, Quote $quote)
    {
        if (!in_array($quote->status, ['draft', 'sent'])) {
            return back()->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'quote_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:quote_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $quote) {
            $quote->update([
                'customer_id' => $validated['customer_id'],
                'quote_date' => $validated['quote_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $quote->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 20,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('quotes.show', $quote)->with('success', 'Devis mis à jour.');
    }

    public function destroy(Quote $quote)
    {
        if (!in_array($quote->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Ce devis ne peut pas être supprimé.');
        }

        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Devis supprimé.');
    }

    public function send(Quote $quote)
    {
        if ($quote->status === 'draft') {
            $quote->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return back()->with('success', 'Devis marqué comme envoyé.');
    }

    public function accept(Quote $quote)
    {
        if ($quote->status === 'sent') {
            $quote->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
        }

        return back()->with('success', 'Devis accepté.');
    }

    public function convertToInvoice(Quote $quote)
    {
        if (!in_array($quote->status, ['accepted']) || $quote->invoice_id) {
            return back()->with('error', 'Ce devis ne peut pas être converti.');
        }

        $invoice = DB::transaction(function () use ($quote) {
            $invoice = Invoice::create([
                'tenant_id' => $quote->tenant_id,
                'customer_id' => $quote->customer_id,
                'quote_id' => $quote->id,
                'user_id' => auth()->id(),
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'notes' => $quote->notes,
                'terms' => $quote->terms,
                'status' => 'draft',
            ]);

            foreach ($quote->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_rate' => $item->tax_rate,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'sort_order' => $item->sort_order,
                ]);
            }

            $quote->update([
                'status' => 'converted',
                'invoice_id' => $invoice->id,
            ]);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Facture créée à partir du devis.');
    }

    public function pdf(Quote $quote)
    {
        $quote->load(['customer', 'items.product', 'tenant']);
        
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'));
        
        return $pdf->download("devis-{$quote->quote_number}.pdf");
    }
}
