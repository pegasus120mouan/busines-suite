<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view_dashboard',

            // Users
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Customers
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',

            // Suppliers
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // Products
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            // Product Categories
            'view_product_categories',
            'create_product_categories',
            'edit_product_categories',
            'delete_product_categories',

            // Warehouses
            'view_warehouses',
            'create_warehouses',
            'edit_warehouses',
            'delete_warehouses',

            // Stock
            'view_stock',
            'manage_stock',
            'transfer_stock',

            // Quotes
            'view_quotes',
            'create_quotes',
            'edit_quotes',
            'delete_quotes',
            'send_quotes',

            // Invoices
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'send_invoices',

            // Payments
            'view_payments',
            'create_payments',
            'delete_payments',

            // Expenses
            'view_expenses',
            'create_expenses',
            'edit_expenses',
            'delete_expenses',
            'approve_expenses',

            // Expense Categories
            'view_expense_categories',
            'create_expense_categories',
            'edit_expense_categories',
            'delete_expense_categories',

            // Purchase Orders
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',

            // Reports
            'view_reports',
            'export_reports',

            // Audit Logs
            'view_audit_logs',

            // Settings
            'view_settings',
            'edit_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // Admin - has all permissions except some system-level ones
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        // Manager - can manage most business operations
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view_dashboard',
            'view_users',
            'view_customers', 'create_customers', 'edit_customers',
            'view_suppliers', 'create_suppliers', 'edit_suppliers',
            'view_products', 'create_products', 'edit_products',
            'view_product_categories', 'create_product_categories', 'edit_product_categories',
            'view_warehouses',
            'view_stock', 'manage_stock', 'transfer_stock',
            'view_quotes', 'create_quotes', 'edit_quotes', 'send_quotes',
            'view_invoices', 'create_invoices', 'edit_invoices', 'send_invoices',
            'view_payments', 'create_payments',
            'view_expenses', 'create_expenses', 'edit_expenses', 'approve_expenses',
            'view_expense_categories',
            'view_purchase_orders', 'create_purchase_orders', 'edit_purchase_orders',
            'view_reports', 'export_reports',
            'view_audit_logs',
        ]);

        // Accountant - focused on financial operations
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'view_dashboard',
            'view_customers',
            'view_suppliers',
            'view_invoices', 'create_invoices', 'edit_invoices', 'send_invoices',
            'view_payments', 'create_payments',
            'view_expenses', 'create_expenses', 'edit_expenses', 'approve_expenses',
            'view_expense_categories', 'create_expense_categories', 'edit_expense_categories',
            'view_quotes',
            'view_reports', 'export_reports',
        ]);

        // Sales - focused on sales operations
        $sales = Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        $sales->syncPermissions([
            'view_dashboard',
            'view_customers', 'create_customers', 'edit_customers',
            'view_products',
            'view_stock',
            'view_quotes', 'create_quotes', 'edit_quotes', 'send_quotes',
            'view_invoices', 'create_invoices', 'send_invoices',
            'view_payments', 'create_payments',
        ]);

        // Warehouse - focused on inventory management
        $warehouse = Role::firstOrCreate(['name' => 'warehouse', 'guard_name' => 'web']);
        $warehouse->syncPermissions([
            'view_dashboard',
            'view_products',
            'view_product_categories',
            'view_warehouses',
            'view_stock', 'manage_stock', 'transfer_stock',
            'view_purchase_orders', 'create_purchase_orders', 'edit_purchase_orders',
            'view_suppliers',
        ]);

        // Viewer - read-only access
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view_dashboard',
            'view_customers',
            'view_suppliers',
            'view_products',
            'view_product_categories',
            'view_warehouses',
            'view_stock',
            'view_quotes',
            'view_invoices',
            'view_payments',
            'view_expenses',
            'view_expense_categories',
            'view_purchase_orders',
        ]);
    }
}
