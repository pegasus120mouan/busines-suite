<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'group_by' => ['nullable', 'in:day,week,month,year'],
        ]);

        $groupBy = $request->group_by ?? 'month';
        $dateFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
        };

        $sales = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$request->from_date, $request->to_date])
            ->selectRaw("DATE_FORMAT(paid_at, '{$dateFormat}') as period, COUNT(*) as count, SUM(total) as total, SUM(tax_amount) as tax")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $topProducts = Product::select('products.id', 'products.name', 'products.sku')
            ->join('invoice_items', 'products.id', '=', 'invoice_items.product_id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_at', [$request->from_date, $request->to_date])
            ->selectRaw('SUM(invoice_items.quantity) as quantity, SUM(invoice_items.total) as revenue')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $topCustomers = Customer::select('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name', 'customers.type')
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_at', [$request->from_date, $request->to_date])
            ->selectRaw('COUNT(invoices.id) as invoice_count, SUM(invoices.total) as revenue')
            ->groupBy('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name', 'customers.type')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'period' => [
                    'from' => $request->from_date,
                    'to' => $request->to_date,
                ],
                'summary' => [
                    'total_revenue' => (float) $sales->sum('total'),
                    'total_tax' => (float) $sales->sum('tax'),
                    'invoice_count' => $sales->sum('count'),
                    'average_invoice' => $sales->count() > 0 ? round($sales->sum('total') / $sales->sum('count'), 2) : 0,
                ],
                'sales_by_period' => $sales,
                'top_products' => $topProducts,
                'top_customers' => $topCustomers,
            ],
        ]);
    }

    public function expenseReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $expenses = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$request->from_date, $request->to_date]);

        $byCategory = (clone $expenses)
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category, expense_categories.color, SUM(expenses.total) as total, COUNT(*) as count')
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->orderByDesc('total')
            ->get();

        $byMonth = (clone $expenses)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(total) as total, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $byPaymentMethod = (clone $expenses)
            ->selectRaw('payment_method, SUM(total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'data' => [
                'period' => [
                    'from' => $request->from_date,
                    'to' => $request->to_date,
                ],
                'summary' => [
                    'total_expenses' => (float) $byCategory->sum('total'),
                    'expense_count' => $byCategory->sum('count'),
                    'average_expense' => $byCategory->sum('count') > 0 ? round($byCategory->sum('total') / $byCategory->sum('count'), 2) : 0,
                ],
                'by_category' => $byCategory,
                'by_month' => $byMonth,
                'by_payment_method' => $byPaymentMethod,
            ],
        ]);
    }

    public function profitLossReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $revenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$request->from_date, $request->to_date])
            ->sum('total');

        $expenses = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$request->from_date, $request->to_date])
            ->sum('total');

        $revenueByMonth = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$request->from_date, $request->to_date])
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $expensesByMonth = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$request->from_date, $request->to_date])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $months = $revenueByMonth->keys()->merge($expensesByMonth->keys())->unique()->sort()->values();

        $profitByMonth = $months->map(fn ($month) => [
            'month' => $month,
            'revenue' => (float) ($revenueByMonth[$month] ?? 0),
            'expenses' => (float) ($expensesByMonth[$month] ?? 0),
            'profit' => (float) (($revenueByMonth[$month] ?? 0) - ($expensesByMonth[$month] ?? 0)),
        ]);

        return response()->json([
            'data' => [
                'period' => [
                    'from' => $request->from_date,
                    'to' => $request->to_date,
                ],
                'summary' => [
                    'total_revenue' => (float) $revenue,
                    'total_expenses' => (float) $expenses,
                    'net_profit' => (float) ($revenue - $expenses),
                    'profit_margin' => $revenue > 0 ? round((($revenue - $expenses) / $revenue) * 100, 2) : 0,
                ],
                'by_month' => $profitByMonth,
            ],
        ]);
    }

    public function receivablesReport(Request $request): JsonResponse
    {
        $aging = [
            'current' => Invoice::unpaid()->where('due_date', '>=', now())->sum('balance_due'),
            '1_30_days' => Invoice::unpaid()->whereBetween('due_date', [now()->subDays(30), now()->subDay()])->sum('balance_due'),
            '31_60_days' => Invoice::unpaid()->whereBetween('due_date', [now()->subDays(60), now()->subDays(31)])->sum('balance_due'),
            '61_90_days' => Invoice::unpaid()->whereBetween('due_date', [now()->subDays(90), now()->subDays(61)])->sum('balance_due'),
            'over_90_days' => Invoice::unpaid()->where('due_date', '<', now()->subDays(90))->sum('balance_due'),
        ];

        $byCustomer = Customer::select('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name', 'customers.type')
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->whereIn('invoices.status', ['sent', 'partial', 'overdue'])
            ->where('invoices.balance_due', '>', 0)
            ->selectRaw('SUM(invoices.balance_due) as balance, COUNT(invoices.id) as invoice_count')
            ->groupBy('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name', 'customers.type')
            ->orderByDesc('balance')
            ->limit(20)
            ->get();

        return response()->json([
            'data' => [
                'summary' => [
                    'total_receivables' => (float) array_sum($aging),
                    'current' => (float) $aging['current'],
                    'overdue' => (float) (array_sum($aging) - $aging['current']),
                ],
                'aging' => $aging,
                'by_customer' => $byCustomer,
            ],
        ]);
    }

    public function cashFlowReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $inflows = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$request->from_date, $request->to_date])
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m-%d') as date, SUM(amount) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $outflows = Expense::where('status', 'paid')
            ->whereBetween('expense_date', [$request->from_date, $request->to_date])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m-%d') as date, SUM(total) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => [
                'period' => [
                    'from' => $request->from_date,
                    'to' => $request->to_date,
                ],
                'summary' => [
                    'total_inflows' => (float) $inflows->sum('total'),
                    'total_outflows' => (float) $outflows->sum('total'),
                    'net_cash_flow' => (float) ($inflows->sum('total') - $outflows->sum('total')),
                ],
                'inflows' => $inflows,
                'outflows' => $outflows,
            ],
        ]);
    }
}
