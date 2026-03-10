<?php

use App\Http\Controllers\Web\AuditLogController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DocumentTemplateController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\ImportExportController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\QuoteController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\ProductCategoryController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\PurchaseOrderController;
use App\Http\Controllers\Web\ReminderController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\StockController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\TaxRateController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\WarehouseController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// Logout (authenticated only)
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Quotes
    Route::post('quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('quotes/{quote}/accept', [QuoteController::class, 'accept'])->name('quotes.accept');
    Route::post('quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::resource('quotes', QuoteController::class);

    // Invoices
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
    Route::resource('invoices', InvoiceController::class);

    // Expenses
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::resource('expenses', ExpenseController::class);

    // Warehouses & Stock
    Route::resource('warehouses', WarehouseController::class);
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('movements', [StockController::class, 'movements'])->name('movements');
        Route::get('adjust', [StockController::class, 'adjustForm'])->name('adjust.form');
        Route::post('adjust', [StockController::class, 'adjust'])->name('adjust');
        Route::get('transfer', [StockController::class, 'transferForm'])->name('transfer.form');
        Route::post('transfer', [StockController::class, 'transfer'])->name('transfer');
    });

    // Purchase Orders
    Route::post('purchase-orders/{purchase_order}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
    Route::post('purchase-orders/{purchase_order}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');
    Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    // Users
    Route::resource('users', UserController::class);

    // Product Categories
    Route::resource('product-categories', ProductCategoryController::class)->except(['show']);

    // Tax Rates
    Route::resource('tax-rates', TaxRateController::class)->except(['show']);

    // Search
    Route::get('search', [SearchController::class, 'index'])->name('search');

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Import/Export
    Route::get('import-export', [ImportExportController::class, 'index'])->name('import-export.index');
    Route::get('import-export/export/{type}', [ImportExportController::class, 'export'])->name('import-export.export');
    Route::post('import-export/import/{type}', [ImportExportController::class, 'import'])->name('import-export.import');
    Route::get('import-export/template/{type}', [ImportExportController::class, 'downloadTemplate'])->name('import-export.template');

    // Reminders
    Route::get('reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::get('reminders/create', [ReminderController::class, 'create'])->name('reminders.create');
    Route::post('reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::post('reminders/{reminder}/mark-sent', [ReminderController::class, 'markAsSent'])->name('reminders.mark-sent');
    Route::post('reminders/{reminder}/cancel', [ReminderController::class, 'cancel'])->name('reminders.cancel');

    // Document Templates
    Route::resource('document-templates', DocumentTemplateController::class)->except(['show']);
    Route::post('document-templates/{document_template}/set-default', [DocumentTemplateController::class, 'setDefault'])->name('document-templates.set-default');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/count', [NotificationController::class, 'count'])->name('notifications.count');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('receivables', [ReportController::class, 'receivables'])->name('receivables');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
    });

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('settings/logo', [SettingsController::class, 'deleteLogo'])->name('settings.delete-logo');
});
