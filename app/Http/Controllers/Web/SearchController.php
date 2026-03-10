<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Expense;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $results = $this->search($query);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($results);
        }

        return view('search.index', compact('query', 'results'));
    }

    private function search(string $query): array
    {
        $results = [];

        // Search Customers
        $customers = Customer::where('company_name', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($customers as $customer) {
            $results[] = [
                'type' => 'customer',
                'type_label' => 'Client',
                'icon' => 'users',
                'title' => $customer->display_name,
                'subtitle' => $customer->email ?? $customer->phone ?? '',
                'url' => route('customers.show', $customer),
            ];
        }

        // Search Invoices
        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")
            ->orWhereHas('customer', function ($q) use ($query) {
                $q->where('company_name', 'like', "%{$query}%")
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->with('customer')
            ->limit(5)
            ->get();

        foreach ($invoices as $invoice) {
            $results[] = [
                'type' => 'invoice',
                'type_label' => 'Facture',
                'icon' => 'document-text',
                'title' => $invoice->invoice_number,
                'subtitle' => $invoice->customer?->display_name ?? 'N/A',
                'url' => route('invoices.show', $invoice),
            ];
        }

        // Search Quotes
        $quotes = Quote::where('quote_number', 'like', "%{$query}%")
            ->orWhereHas('customer', function ($q) use ($query) {
                $q->where('company_name', 'like', "%{$query}%")
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->with('customer')
            ->limit(5)
            ->get();

        foreach ($quotes as $quote) {
            $results[] = [
                'type' => 'quote',
                'type_label' => 'Devis',
                'icon' => 'document',
                'title' => $quote->quote_number,
                'subtitle' => $quote->customer?->display_name ?? 'N/A',
                'url' => route('quotes.show', $quote),
            ];
        }

        // Search Products
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'type_label' => 'Produit',
                'icon' => 'cube',
                'title' => $product->name,
                'subtitle' => $product->sku ?? '',
                'url' => route('products.show', $product),
            ];
        }

        // Search Suppliers
        $suppliers = Supplier::where('company_name', 'like', "%{$query}%")
            ->orWhere('contact_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($suppliers as $supplier) {
            $results[] = [
                'type' => 'supplier',
                'type_label' => 'Fournisseur',
                'icon' => 'truck',
                'title' => $supplier->company_name,
                'subtitle' => $supplier->contact_name ?? $supplier->email ?? '',
                'url' => route('suppliers.show', $supplier),
            ];
        }

        // Search Expenses
        $expenses = Expense::where('reference', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($expenses as $expense) {
            $results[] = [
                'type' => 'expense',
                'type_label' => 'Dépense',
                'icon' => 'credit-card',
                'title' => $expense->reference ?? 'Dépense #' . $expense->id,
                'subtitle' => $expense->description ?? '',
                'url' => route('expenses.show', $expense),
            ];
        }

        return $results;
    }
}
