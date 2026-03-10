<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Supplier;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        
        if (!$tenant) {
            return;
        }

        // Create Suppliers
        $suppliers = [
            ['company_name' => 'Tech Supplies SARL', 'contact_name' => 'Jean Dupont', 'email' => 'contact@techsupplies.fr', 'phone' => '01 23 45 67 89', 'status' => 'active'],
            ['company_name' => 'Office Pro', 'contact_name' => 'Marie Martin', 'email' => 'info@officepro.fr', 'phone' => '01 98 76 54 32', 'status' => 'active'],
            ['company_name' => 'Global Import', 'contact_name' => 'Pierre Durand', 'email' => 'sales@globalimport.com', 'phone' => '01 11 22 33 44', 'status' => 'active'],
        ];

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(
                ['tenant_id' => $tenant->id, 'email' => $data['email']],
                array_merge($data, ['tenant_id' => $tenant->id])
            );
        }

        // Create Customers
        $customers = [
            ['type' => 'company', 'company_name' => 'Acme Corporation', 'email' => 'contact@acme.fr', 'phone' => '01 40 50 60 70', 'billing_address' => '123 Rue de la Paix', 'billing_city' => 'Paris', 'billing_postal_code' => '75001', 'billing_country' => 'FR', 'status' => 'active'],
            ['type' => 'company', 'company_name' => 'StartUp Innovation', 'email' => 'hello@startup-innov.fr', 'phone' => '01 55 66 77 88', 'billing_address' => '45 Avenue des Champs', 'billing_city' => 'Lyon', 'billing_postal_code' => '69001', 'billing_country' => 'FR', 'status' => 'active'],
            ['type' => 'individual', 'first_name' => 'Sophie', 'last_name' => 'Bernard', 'email' => 'sophie.bernard@email.fr', 'phone' => '06 12 34 56 78', 'billing_address' => '8 Rue Victor Hugo', 'billing_city' => 'Marseille', 'billing_postal_code' => '13001', 'billing_country' => 'FR', 'status' => 'active'],
            ['type' => 'company', 'company_name' => 'Digital Agency', 'email' => 'info@digitalagency.fr', 'phone' => '01 99 88 77 66', 'billing_address' => '200 Boulevard Haussmann', 'billing_city' => 'Paris', 'billing_postal_code' => '75008', 'billing_country' => 'FR', 'status' => 'active'],
            ['type' => 'individual', 'first_name' => 'Marc', 'last_name' => 'Lefebvre', 'email' => 'marc.lefebvre@gmail.com', 'phone' => '06 98 76 54 32', 'billing_address' => '15 Place Bellecour', 'billing_city' => 'Lyon', 'billing_postal_code' => '69002', 'billing_country' => 'FR', 'status' => 'active'],
        ];

        $createdCustomers = [];
        foreach ($customers as $data) {
            $createdCustomers[] = Customer::firstOrCreate(
                ['tenant_id' => $tenant->id, 'email' => $data['email']],
                array_merge($data, ['tenant_id' => $tenant->id])
            );
        }

        // Create Products
        $products = [
            ['name' => 'Consultation (1h)', 'type' => 'service', 'sku' => 'SRV-001', 'selling_price' => 150.00, 'tax_rate' => 20, 'is_active' => true],
            ['name' => 'Développement Web (jour)', 'type' => 'service', 'sku' => 'SRV-002', 'selling_price' => 600.00, 'tax_rate' => 20, 'is_active' => true],
            ['name' => 'Licence Logiciel Annuelle', 'type' => 'product', 'sku' => 'PRD-001', 'selling_price' => 299.00, 'tax_rate' => 20, 'is_active' => true],
            ['name' => 'Formation (demi-journée)', 'type' => 'service', 'sku' => 'SRV-003', 'selling_price' => 450.00, 'tax_rate' => 20, 'is_active' => true],
            ['name' => 'Support Technique (mois)', 'type' => 'service', 'sku' => 'SRV-004', 'selling_price' => 200.00, 'tax_rate' => 20, 'is_active' => true],
            ['name' => 'Hébergement Web (an)', 'type' => 'service', 'sku' => 'SRV-005', 'selling_price' => 120.00, 'tax_rate' => 20, 'is_active' => true],
        ];

        $createdProducts = [];
        foreach ($products as $data) {
            $createdProducts[] = Product::firstOrCreate(
                ['tenant_id' => $tenant->id, 'sku' => $data['sku']],
                array_merge($data, ['tenant_id' => $tenant->id])
            );
        }

        // Create Invoices
        $user = $tenant->users()->first();
        
        if ($user && count($createdCustomers) > 0 && count($createdProducts) > 0) {
            // Invoice 1 - Paid
            $invoice1 = Invoice::firstOrCreate(
                ['tenant_id' => $tenant->id, 'invoice_number' => 'FAC-2024-001'],
                [
                    'tenant_id' => $tenant->id,
                    'customer_id' => $createdCustomers[0]->id,
                    'user_id' => $user->id,
                    'invoice_number' => 'FAC-2024-001',
                    'invoice_date' => now()->subDays(30),
                    'due_date' => now()->subDays(0),
                    'status' => 'paid',
                    'subtotal' => 1500.00,
                    'tax_amount' => 300.00,
                    'total' => 1800.00,
                    'amount_paid' => 1800.00,
                    'balance_due' => 0,
                    'notes' => 'Merci pour votre confiance.',
                ]
            );

            if ($invoice1->wasRecentlyCreated) {
                InvoiceItem::create([
                    'invoice_id' => $invoice1->id,
                    'product_id' => $createdProducts[1]->id,
                    'description' => 'Développement Web - 2.5 jours',
                    'quantity' => 2.5,
                    'unit_price' => 600.00,
                    'tax_rate' => 20,
                    'subtotal' => 1500.00,
                    'tax_amount' => 300.00,
                    'total' => 1800.00,
                ]);
            }

            // Invoice 2 - Sent (pending)
            $invoice2 = Invoice::firstOrCreate(
                ['tenant_id' => $tenant->id, 'invoice_number' => 'FAC-2024-002'],
                [
                    'tenant_id' => $tenant->id,
                    'customer_id' => $createdCustomers[1]->id,
                    'user_id' => $user->id,
                    'invoice_number' => 'FAC-2024-002',
                    'invoice_date' => now()->subDays(15),
                    'due_date' => now()->addDays(15),
                    'status' => 'sent',
                    'subtotal' => 749.00,
                    'tax_amount' => 149.80,
                    'total' => 898.80,
                    'amount_paid' => 0,
                    'balance_due' => 898.80,
                ]
            );

            if ($invoice2->wasRecentlyCreated) {
                InvoiceItem::create([
                    'invoice_id' => $invoice2->id,
                    'product_id' => $createdProducts[0]->id,
                    'description' => 'Consultation - 3 heures',
                    'quantity' => 3,
                    'unit_price' => 150.00,
                    'tax_rate' => 20,
                    'subtotal' => 450.00,
                    'tax_amount' => 90.00,
                    'total' => 540.00,
                ]);
                InvoiceItem::create([
                    'invoice_id' => $invoice2->id,
                    'product_id' => $createdProducts[2]->id,
                    'description' => 'Licence Logiciel Annuelle',
                    'quantity' => 1,
                    'unit_price' => 299.00,
                    'tax_rate' => 20,
                    'subtotal' => 299.00,
                    'tax_amount' => 59.80,
                    'total' => 358.80,
                ]);
            }

            // Invoice 3 - Overdue
            $invoice3 = Invoice::firstOrCreate(
                ['tenant_id' => $tenant->id, 'invoice_number' => 'FAC-2024-003'],
                [
                    'tenant_id' => $tenant->id,
                    'customer_id' => $createdCustomers[3]->id,
                    'user_id' => $user->id,
                    'invoice_number' => 'FAC-2024-003',
                    'invoice_date' => now()->subDays(45),
                    'due_date' => now()->subDays(15),
                    'status' => 'overdue',
                    'subtotal' => 2400.00,
                    'tax_amount' => 480.00,
                    'total' => 2880.00,
                    'amount_paid' => 0,
                    'balance_due' => 2880.00,
                ]
            );

            if ($invoice3->wasRecentlyCreated) {
                InvoiceItem::create([
                    'invoice_id' => $invoice3->id,
                    'product_id' => $createdProducts[1]->id,
                    'description' => 'Développement Web - 4 jours',
                    'quantity' => 4,
                    'unit_price' => 600.00,
                    'tax_rate' => 20,
                    'subtotal' => 2400.00,
                    'tax_amount' => 480.00,
                    'total' => 2880.00,
                ]);
            }

            // Create a Quote
            $quote1 = Quote::firstOrCreate(
                ['tenant_id' => $tenant->id, 'quote_number' => 'DEV-2024-001'],
                [
                    'tenant_id' => $tenant->id,
                    'customer_id' => $createdCustomers[2]->id,
                    'user_id' => $user->id,
                    'quote_number' => 'DEV-2024-001',
                    'quote_date' => now()->subDays(5),
                    'valid_until' => now()->addDays(25),
                    'status' => 'sent',
                    'subtotal' => 1350.00,
                    'tax_amount' => 270.00,
                    'total' => 1620.00,
                ]
            );

            if ($quote1->wasRecentlyCreated) {
                QuoteItem::create([
                    'quote_id' => $quote1->id,
                    'product_id' => $createdProducts[3]->id,
                    'description' => 'Formation - 3 demi-journées',
                    'quantity' => 3,
                    'unit_price' => 450.00,
                    'tax_rate' => 20,
                    'subtotal' => 1350.00,
                    'tax_amount' => 270.00,
                    'total' => 1620.00,
                ]);
            }
        }

        // Create Expense Categories if not exist
        $categories = ExpenseCategory::where('tenant_id', $tenant->id)->get();
        
        if ($categories->isEmpty()) {
            $defaultCategories = [
                ['name' => 'Fournitures de bureau', 'color' => '#3B82F6'],
                ['name' => 'Déplacements', 'color' => '#10B981'],
                ['name' => 'Repas', 'color' => '#F59E0B'],
                ['name' => 'Logiciels & Abonnements', 'color' => '#8B5CF6'],
                ['name' => 'Marketing', 'color' => '#EC4899'],
            ];
            
            foreach ($defaultCategories as $cat) {
                ExpenseCategory::create(array_merge($cat, ['tenant_id' => $tenant->id]));
            }
            
            $categories = ExpenseCategory::where('tenant_id', $tenant->id)->get();
        }

        // Create Expenses
        if ($user && $categories->count() > 0) {
            $expenses = [
                ['description' => 'Achat fournitures bureau', 'amount' => 85.00, 'tax_amount' => 17.00, 'total' => 102.00, 'expense_date' => now()->subDays(10), 'status' => 'approved', 'category_id' => $categories[0]->id],
                ['description' => 'Déplacement client Paris', 'amount' => 125.00, 'tax_amount' => 0, 'total' => 125.00, 'expense_date' => now()->subDays(7), 'status' => 'pending', 'category_id' => $categories[1]->id],
                ['description' => 'Repas équipe', 'amount' => 95.00, 'tax_amount' => 9.50, 'total' => 104.50, 'expense_date' => now()->subDays(3), 'status' => 'pending', 'category_id' => $categories[2]->id],
                ['description' => 'Abonnement GitHub', 'amount' => 19.00, 'tax_amount' => 3.80, 'total' => 22.80, 'expense_date' => now()->subDays(1), 'status' => 'approved', 'category_id' => $categories[3]->id],
            ];

            foreach ($expenses as $data) {
                Expense::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'description' => $data['description'], 'expense_date' => $data['expense_date']],
                    array_merge($data, ['tenant_id' => $tenant->id, 'user_id' => $user->id])
                );
            }
        }
    }
}
