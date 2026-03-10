<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NumberSequenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaxRateController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ProvisioningController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Provisioning API (called by saas-admin)
Route::prefix('provisioning')->group(function () {
    Route::post('tenants', [ProvisioningController::class, 'createTenant']);
});

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // Profile
    Route::put('profile', [UserController::class, 'updateProfile']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view_dashboard');

    // Users
    Route::apiResource('users', UserController::class)
        ->middleware('permission:view_users|create_users|edit_users|delete_users');

    // Customers
    Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])
        ->middleware('permission:edit_customers');
    Route::apiResource('customers', CustomerController::class)->names('api.customers');

    // Suppliers
    Route::apiResource('suppliers', SupplierController::class)->names('api.suppliers');

    // Products
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])
        ->middleware('permission:view_products');
    Route::apiResource('products', ProductController::class)->names('api.products');

    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);

    // Stock Management
    Route::prefix('stock')->middleware('permission:view_stock')->group(function () {
        Route::get('/', [StockController::class, 'index']);
        Route::get('movements', [StockController::class, 'movements']);
        Route::post('adjust', [StockController::class, 'adjust'])
            ->middleware('permission:manage_stock');
        Route::post('transfer', [StockController::class, 'transfer'])
            ->middleware('permission:transfer_stock');
        Route::post('receive', [StockController::class, 'receive'])
            ->middleware('permission:manage_stock');
    });

    // Quotes
    Route::prefix('quotes')->group(function () {
        Route::post('{quote}/send', [QuoteController::class, 'send'])
            ->middleware('permission:send_quotes');
        Route::post('{quote}/accept', [QuoteController::class, 'accept'])
            ->middleware('permission:edit_quotes');
        Route::post('{quote}/reject', [QuoteController::class, 'reject'])
            ->middleware('permission:edit_quotes');
    });
    Route::apiResource('quotes', QuoteController::class)->names('api.quotes');

    // Invoices
    Route::prefix('invoices')->group(function () {
        Route::get('statistics', [InvoiceController::class, 'statistics'])
            ->middleware('permission:view_reports');
        Route::post('{invoice}/send', [InvoiceController::class, 'send'])
            ->middleware('permission:send_invoices');
        Route::post('from-quote/{quote}', [InvoiceController::class, 'createFromQuote'])
            ->middleware('permission:create_invoices');
    });
    Route::apiResource('invoices', InvoiceController::class)->names('api.invoices');

    // Payments
    Route::apiResource('payments', PaymentController::class)
        ->only(['index', 'store', 'show', 'destroy'])->names('api.payments');

    // Expenses
    Route::prefix('expenses')->group(function () {
        Route::get('statistics', [ExpenseController::class, 'statistics'])
            ->middleware('permission:view_reports');
        Route::post('{expense}/approve', [ExpenseController::class, 'approve'])
            ->middleware('permission:approve_expenses');
        Route::post('{expense}/reject', [ExpenseController::class, 'reject'])
            ->middleware('permission:approve_expenses');
        Route::post('{expense}/mark-paid', [ExpenseController::class, 'markAsPaid'])
            ->middleware('permission:edit_expenses');
    });
    Route::apiResource('expenses', ExpenseController::class)->names('api.expenses');

    // Audit Logs
    Route::prefix('audit-logs')->middleware('permission:view_audit_logs')->group(function () {
        Route::get('/', [AuditLogController::class, 'index']);
        Route::get('statistics', [AuditLogController::class, 'statistics']);
        Route::get('{auditLog}', [AuditLogController::class, 'show']);
    });

    // Product Categories
    Route::get('product-categories/tree', [ProductCategoryController::class, 'tree']);
    Route::apiResource('product-categories', ProductCategoryController::class);

    // Expense Categories
    Route::apiResource('expense-categories', ExpenseCategoryController::class);

    // Purchase Orders
    Route::prefix('purchase-orders')->group(function () {
        Route::post('{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])
            ->middleware('permission:edit_purchase_orders');
        Route::post('{purchaseOrder}/confirm', [PurchaseOrderController::class, 'confirm'])
            ->middleware('permission:edit_purchase_orders');
        Route::post('{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])
            ->middleware('permission:edit_purchase_orders');
    });
    Route::apiResource('purchase-orders', PurchaseOrderController::class);

    // Tenant Settings
    Route::prefix('tenant')->middleware('permission:view_settings')->group(function () {
        Route::get('/', [TenantController::class, 'show']);
        Route::put('/', [TenantController::class, 'update'])
            ->middleware('permission:edit_settings');
        Route::patch('settings', [TenantController::class, 'updateSettings'])
            ->middleware('permission:edit_settings');
    });

    // Roles & Permissions
    Route::prefix('roles')->middleware('role:admin')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('permissions', [RoleController::class, 'permissions']);
        Route::get('{role}', [RoleController::class, 'show']);
        Route::put('{role}', [RoleController::class, 'update']);
        Route::delete('{role}', [RoleController::class, 'destroy']);
    });

    // Tax Rates
    Route::apiResource('tax-rates', TaxRateController::class)
        ->middleware('permission:view_settings');

    // Number Sequences
    Route::prefix('number-sequences')->middleware('permission:view_settings')->group(function () {
        Route::get('/', [NumberSequenceController::class, 'index']);
        Route::get('{numberSequence}', [NumberSequenceController::class, 'show']);
        Route::put('{numberSequence}', [NumberSequenceController::class, 'update'])
            ->middleware('permission:edit_settings');
    });

    // Reports
    Route::prefix('reports')->middleware('permission:view_reports')->group(function () {
        Route::get('sales', [ReportController::class, 'salesReport']);
        Route::get('expenses', [ReportController::class, 'expenseReport']);
        Route::get('profit-loss', [ReportController::class, 'profitLossReport']);
        Route::get('receivables', [ReportController::class, 'receivablesReport']);
        Route::get('cash-flow', [ReportController::class, 'cashFlowReport']);
    });
});
