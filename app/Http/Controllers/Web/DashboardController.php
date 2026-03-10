<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get statistics
        $stats = [
            'revenue' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('total'),
            'expenses' => Expense::whereIn('status', ['approved', 'paid'])
                ->whereMonth('expense_date', now()->month)
                ->sum('total'),
            'pending_invoices' => Invoice::whereIn('status', ['sent', 'partial'])
                ->sum('balance_due'),
            'customers_count' => Customer::where('status', 'active')->count(),
            'products_count' => Product::where('is_active', true)->count(),
            'quotes_pending' => Quote::where('status', 'sent')->count(),
        ];

        $stats['profit'] = $stats['revenue'] - $stats['expenses'];

        // Monthly revenue trend (last 12 months)
        $monthlyRevenue = $this->getMonthlyRevenue();
        $monthlyExpenses = $this->getMonthlyExpenses();

        // Recent invoices
        $recentInvoices = Invoice::with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent quotes
        $recentQuotes = Quote::with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Overdue invoices
        $overdueInvoices = Invoice::with('customer')
            ->overdue()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('track_stock', true)
            ->where('is_active', true)
            ->whereRaw('(SELECT COALESCE(SUM(quantity), 0) FROM product_stocks WHERE product_id = products.id) <= min_stock_level')
            ->limit(5)
            ->get();

        // Top customers by revenue
        $topCustomers = Customer::select([
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                'customers.email',
                'customers.phone',
                'customers.company_name',
                'customers.tenant_id',
                'customers.created_at',
                'customers.updated_at'
            ])
            ->selectRaw('COALESCE(SUM(invoices.total), 0) as total_revenue')
            ->leftJoin('invoices', function ($join) {
                $join->on('customers.id', '=', 'invoices.customer_id')
                    ->where('invoices.status', '=', 'paid');
            })
            ->whereNull('customers.deleted_at')
            ->groupBy([
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                'customers.email',
                'customers.phone',
                'customers.company_name',
                'customers.tenant_id',
                'customers.created_at',
                'customers.updated_at'
            ])
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Invoice status distribution
        $invoicesByStatus = Invoice::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard.index', compact(
            'stats',
            'monthlyRevenue',
            'monthlyExpenses',
            'recentInvoices',
            'recentQuotes',
            'overdueInvoices',
            'lowStockProducts',
            'topCustomers',
            'invoicesByStatus'
        ));
    }

    private function getMonthlyRevenue(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total');
            
            $data[] = [
                'month' => $date->translatedFormat('M Y'),
                'short' => $date->translatedFormat('M'),
                'value' => (float) $revenue,
            ];
        }
        return $data;
    }

    private function getMonthlyExpenses(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $expenses = Expense::whereIn('status', ['approved', 'paid'])
                ->whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('total');
            
            $data[] = [
                'month' => $date->translatedFormat('M Y'),
                'short' => $date->translatedFormat('M'),
                'value' => (float) $expenses,
            ];
        }
        return $data;
    }
}
