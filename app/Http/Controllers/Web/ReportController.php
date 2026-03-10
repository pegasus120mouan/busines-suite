<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->endOfMonth();

        $invoices = Invoice::with('customer')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $summary = [
            'total_invoiced' => $invoices->sum('total'),
            'total_paid' => $invoices->sum('amount_paid'),
            'total_outstanding' => $invoices->sum('balance_due'),
            'invoice_count' => $invoices->count(),
            'paid_count' => $invoices->where('status', 'paid')->count(),
            'overdue_count' => $invoices->where('status', 'overdue')->count(),
        ];

        $salesByMonth = Invoice::select(
            DB::raw('YEAR(invoice_date) as year'),
            DB::raw('MONTH(invoice_date) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(amount_paid) as paid'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('invoice_date', [$startDate->copy()->startOfYear(), $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $topCustomers = Customer::select('customers.*')
            ->selectRaw('SUM(invoices.total) as total_sales')
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->groupBy('customers.id')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return view('reports.sales', compact('invoices', 'summary', 'salesByMonth', 'topCustomers', 'startDate', 'endDate'));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->endOfMonth();

        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $summary = [
            'total_expenses' => $expenses->sum('total'),
            'approved_total' => $expenses->whereIn('status', ['approved', 'paid'])->sum('total'),
            'pending_total' => $expenses->where('status', 'pending')->sum('total'),
            'expense_count' => $expenses->count(),
        ];

        $expensesByCategory = Expense::select('expense_category_id')
            ->selectRaw('SUM(total) as total')
            ->with('category')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'paid'])
            ->groupBy('expense_category_id')
            ->orderByDesc('total')
            ->get();

        $expensesByMonth = Expense::select(
            DB::raw('YEAR(expense_date) as year'),
            DB::raw('MONTH(expense_date) as month'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('expense_date', [$startDate->copy()->startOfYear(), $endDate])
            ->whereIn('status', ['approved', 'paid'])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('reports.expenses', compact('expenses', 'summary', 'expensesByCategory', 'expensesByMonth', 'startDate', 'endDate'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->endOfMonth();

        $revenue = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total');

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'paid'])
            ->sum('total');

        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        $monthlyData = collect();
        $currentDate = $startDate->copy()->startOfMonth();
        
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $monthRevenue = Invoice::whereBetween('invoice_date', [$monthStart, $monthEnd])
                ->where('status', 'paid')
                ->sum('total');

            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->whereIn('status', ['approved', 'paid'])
                ->sum('total');

            $monthlyData->push([
                'month' => $currentDate->format('M Y'),
                'revenue' => $monthRevenue,
                'expenses' => $monthExpenses,
                'profit' => $monthRevenue - $monthExpenses,
            ]);

            $currentDate->addMonth();
        }

        return view('reports.profit-loss', compact('revenue', 'expenses', 'profit', 'margin', 'monthlyData', 'startDate', 'endDate'));
    }

    public function receivables(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->orderBy('due_date')
            ->get();

        $summary = [
            'total_outstanding' => $invoices->sum('balance_due'),
            'current' => $invoices->filter(fn ($i) => $i->due_date >= now())->sum('balance_due'),
            'overdue_1_30' => $invoices->filter(fn ($i) => $i->due_date < now() && $i->due_date >= now()->subDays(30))->sum('balance_due'),
            'overdue_31_60' => $invoices->filter(fn ($i) => $i->due_date < now()->subDays(30) && $i->due_date >= now()->subDays(60))->sum('balance_due'),
            'overdue_61_90' => $invoices->filter(fn ($i) => $i->due_date < now()->subDays(60) && $i->due_date >= now()->subDays(90))->sum('balance_due'),
            'overdue_90_plus' => $invoices->filter(fn ($i) => $i->due_date < now()->subDays(90))->sum('balance_due'),
        ];

        $byCustomer = Customer::select('customers.*')
            ->selectRaw('SUM(invoices.balance_due) as total_due')
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->whereIn('invoices.status', ['sent', 'partial', 'overdue'])
            ->where('invoices.balance_due', '>', 0)
            ->groupBy('customers.id')
            ->orderByDesc('total_due')
            ->get();

        return view('reports.receivables', compact('invoices', 'summary', 'byCustomer'));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(['category', 'stocks.warehouse'])
            ->where('track_stock', true)
            ->get()
            ->map(function ($product) {
                $product->total_stock = $product->stocks->sum('quantity');
                $product->stock_value = $product->total_stock * $product->sale_price;
                return $product;
            });

        $summary = [
            'total_products' => $products->count(),
            'total_stock_value' => $products->sum('stock_value'),
            'low_stock_count' => $products->filter(fn ($p) => $p->total_stock <= $p->min_stock_level)->count(),
            'out_of_stock_count' => $products->filter(fn ($p) => $p->total_stock <= 0)->count(),
        ];

        $lowStockProducts = $products->filter(fn ($p) => $p->total_stock <= $p->min_stock_level)->sortBy('total_stock');

        return view('reports.inventory', compact('products', 'summary', 'lowStockProducts'));
    }
}
