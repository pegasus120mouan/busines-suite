<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        return response()->json([
            'data' => [
                'summary' => $this->getSummary($fromDate, $toDate),
                'revenue' => $this->getRevenueStats($fromDate, $toDate),
                'expenses' => $this->getExpenseStats($fromDate, $toDate),
                'invoices' => $this->getInvoiceStats(),
                'quotes' => $this->getQuoteStats(),
                'top_customers' => $this->getTopCustomers($fromDate, $toDate),
                'top_products' => $this->getTopProducts($fromDate, $toDate),
                'recent_invoices' => $this->getRecentInvoices(),
                'recent_payments' => $this->getRecentPayments(),
                'low_stock_products' => $this->getLowStockProducts(),
            ],
        ]);
    }

    protected function getSummary(string $fromDate, string $toDate): array
    {
        $revenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->sum('total');

        $expenses = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->sum('total');

        $pendingInvoices = Invoice::whereIn('status', ['sent', 'partial'])
            ->sum('balance_due');

        $overdueInvoices = Invoice::overdue()->sum('balance_due');

        return [
            'revenue' => (float) $revenue,
            'expenses' => (float) $expenses,
            'profit' => (float) ($revenue - $expenses),
            'pending_invoices' => (float) $pendingInvoices,
            'overdue_invoices' => (float) $overdueInvoices,
            'customers_count' => Customer::where('status', 'active')->count(),
            'products_count' => Product::where('is_active', true)->count(),
        ];
    }

    protected function getRevenueStats(string $fromDate, string $toDate): array
    {
        $daily = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->selectRaw('DATE(paid_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthly = Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->selectRaw('MONTH(paid_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'daily' => $daily,
            'monthly' => $monthly,
            'total' => (float) $daily->sum('total'),
        ];
    }

    protected function getExpenseStats(string $fromDate, string $toDate): array
    {
        $byCategory = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category, expense_categories.color, SUM(expenses.total) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->orderByDesc('total')
            ->get();

        $monthly = Expense::whereIn('status', ['approved', 'paid'])
            ->whereYear('expense_date', now()->year)
            ->selectRaw('MONTH(expense_date) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'by_category' => $byCategory,
            'monthly' => $monthly,
            'total' => (float) $byCategory->sum('total'),
        ];
    }

    protected function getInvoiceStats(): array
    {
        $byStatus = Invoice::selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'by_status' => $byStatus,
            'total_count' => Invoice::count(),
            'draft_count' => Invoice::where('status', 'draft')->count(),
            'sent_count' => Invoice::where('status', 'sent')->count(),
            'paid_count' => Invoice::where('status', 'paid')->count(),
            'overdue_count' => Invoice::overdue()->count(),
        ];
    }

    protected function getQuoteStats(): array
    {
        $byStatus = Quote::selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $conversionRate = Quote::whereIn('status', ['accepted', 'converted'])->count() /
            max(Quote::whereNotIn('status', ['draft'])->count(), 1) * 100;

        return [
            'by_status' => $byStatus,
            'total_count' => Quote::count(),
            'pending_count' => Quote::where('status', 'sent')->count(),
            'accepted_count' => Quote::where('status', 'accepted')->count(),
            'conversion_rate' => round($conversionRate, 2),
        ];
    }

    protected function getTopCustomers(string $fromDate, string $toDate, int $limit = 5): array
    {
        return Customer::select('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name', 'customers.type')
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_at', [$fromDate, $toDate])
            ->selectRaw('SUM(invoices.total) as total_revenue')
            ->groupBy('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name', 'customers.type')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function getTopProducts(string $fromDate, string $toDate, int $limit = 5): array
    {
        return Product::select('products.id', 'products.name', 'products.sku')
            ->join('invoice_items', 'products.id', '=', 'invoice_items.product_id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_at', [$fromDate, $toDate])
            ->selectRaw('SUM(invoice_items.quantity) as total_quantity, SUM(invoice_items.total) as total_revenue')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function getRecentInvoices(int $limit = 5): array
    {
        return Invoice::with('customer:id,company_name,first_name,last_name,type')
            ->select('id', 'customer_id', 'invoice_number', 'total', 'status', 'invoice_date', 'due_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function getRecentPayments(int $limit = 5): array
    {
        return Payment::with(['invoice:id,invoice_number,customer_id', 'invoice.customer:id,company_name,first_name,last_name,type'])
            ->select('id', 'invoice_id', 'payment_number', 'amount', 'method', 'payment_date')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function getLowStockProducts(int $limit = 10): array
    {
        return Product::where('track_stock', true)
            ->where('is_active', true)
            ->whereRaw('(SELECT COALESCE(SUM(quantity), 0) FROM product_stocks WHERE product_id = products.id) <= min_stock_level')
            ->select('id', 'name', 'sku', 'min_stock_level')
            ->withSum('stocks', 'quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
