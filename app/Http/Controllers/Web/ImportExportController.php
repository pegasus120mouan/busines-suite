<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportExportController extends Controller
{
    public function index()
    {
        return view('import-export.index');
    }

    public function export(string $type)
    {
        return match ($type) {
            'customers' => $this->exportCustomers(),
            'products' => $this->exportProducts(),
            'invoices' => $this->exportInvoices(),
            'suppliers' => $this->exportSuppliers(),
            default => abort(404),
        };
    }

    public function import(Request $request, string $type)
    {
        return match ($type) {
            'customers' => $this->importCustomers($request),
            'products' => $this->importProducts($request),
            default => abort(404),
        };
    }

    // EXPORT METHODS
    public function exportCustomers()
    {
        $customers = Customer::all();
        $filename = 'clients_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Type', 'Société', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Adresse', 'Code Postal', 'Ville', 'Pays', 'SIRET', 'TVA Intra', 'Statut'];

        $callback = function () use ($customers, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns, ';');

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->type,
                    $customer->company_name,
                    $customer->first_name,
                    $customer->last_name,
                    $customer->email,
                    $customer->phone,
                    $customer->address,
                    $customer->postal_code,
                    $customer->city,
                    $customer->country,
                    $customer->siret,
                    $customer->vat_number,
                    $customer->status,
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProducts()
    {
        $products = Product::with('category')->get();
        $filename = 'produits_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'SKU', 'Nom', 'Description', 'Catégorie', 'Prix Achat', 'Prix Vente', 'Taux TVA', 'Unité', 'Stock Min', 'Actif'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->sku,
                    $product->name,
                    $product->description,
                    $product->category?->name,
                    $product->purchase_price,
                    $product->selling_price,
                    $product->tax_rate,
                    $product->unit,
                    $product->min_stock_level,
                    $product->is_active ? 'Oui' : 'Non',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportInvoices()
    {
        $invoices = Invoice::with('customer')->get();
        $filename = 'factures_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Numéro', 'Client', 'Date', 'Échéance', 'Sous-total', 'TVA', 'Total', 'Payé', 'Reste dû', 'Statut'];

        $callback = function () use ($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->id,
                    $invoice->invoice_number,
                    $invoice->customer?->display_name,
                    $invoice->invoice_date->format('d/m/Y'),
                    $invoice->due_date->format('d/m/Y'),
                    $invoice->subtotal,
                    $invoice->tax_amount,
                    $invoice->total,
                    $invoice->amount_paid,
                    $invoice->balance_due,
                    $invoice->status,
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSuppliers()
    {
        $suppliers = Supplier::all();
        $filename = 'fournisseurs_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Société', 'Contact', 'Email', 'Téléphone', 'Adresse', 'Code Postal', 'Ville', 'Pays', 'SIRET', 'TVA Intra', 'Statut'];

        $callback = function () use ($suppliers, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->id,
                    $supplier->company_name,
                    $supplier->contact_name,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->address,
                    $supplier->postal_code,
                    $supplier->city,
                    $supplier->country,
                    $supplier->siret,
                    $supplier->vat_number,
                    $supplier->status,
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // IMPORT METHODS
    public function importCustomers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ';');
            $row = 1;

            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $row++;
                try {
                    if (count($data) < 6) continue;

                    Customer::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'type' => $data[1] ?? 'company',
                        'company_name' => $data[2] ?? null,
                        'first_name' => $data[3] ?? null,
                        'last_name' => $data[4] ?? null,
                        'email' => $data[5] ?? null,
                        'phone' => $data[6] ?? null,
                        'address' => $data[7] ?? null,
                        'postal_code' => $data[8] ?? null,
                        'city' => $data[9] ?? null,
                        'country' => $data[10] ?? 'France',
                        'siret' => $data[11] ?? null,
                        'vat_number' => $data[12] ?? null,
                        'status' => $data[13] ?? 'active',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne $row: " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        $message = "$imported client(s) importé(s) avec succès.";
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' erreur(s).';
        }

        return back()->with('success', $message)->with('import_errors', $errors);
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ';');
            $row = 1;

            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $row++;
                try {
                    if (count($data) < 3) continue;

                    Product::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'sku' => $data[1] ?? null,
                        'name' => $data[2],
                        'description' => $data[3] ?? null,
                        'purchase_price' => floatval(str_replace(',', '.', $data[5] ?? 0)),
                        'selling_price' => floatval(str_replace(',', '.', $data[6] ?? 0)),
                        'tax_rate' => floatval(str_replace(',', '.', $data[7] ?? 20)),
                        'unit' => $data[8] ?? 'unit',
                        'min_stock_level' => intval($data[9] ?? 0),
                        'is_active' => ($data[10] ?? 'Oui') === 'Oui',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne $row: " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        $message = "$imported produit(s) importé(s) avec succès.";
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' erreur(s).';
        }

        return back()->with('success', $message)->with('import_errors', $errors);
    }

    public function downloadTemplate(string $type)
    {
        $templates = [
            'customers' => [
                'filename' => 'modele_clients.csv',
                'columns' => ['ID', 'Type', 'Société', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Adresse', 'Code Postal', 'Ville', 'Pays', 'SIRET', 'TVA Intra', 'Statut'],
                'example' => ['', 'company', 'Ma Société SARL', 'Jean', 'Dupont', 'contact@masociete.fr', '0123456789', '123 rue Example', '75001', 'Paris', 'France', '12345678901234', 'FR12345678901', 'active'],
            ],
            'products' => [
                'filename' => 'modele_produits.csv',
                'columns' => ['ID', 'SKU', 'Nom', 'Description', 'Catégorie', 'Prix Achat', 'Prix Vente', 'Taux TVA', 'Unité', 'Stock Min', 'Actif'],
                'example' => ['', 'PROD001', 'Mon Produit', 'Description du produit', '', '50.00', '100.00', '20', 'unit', '5', 'Oui'],
            ],
        ];

        if (!isset($templates[$type])) {
            abort(404);
        }

        $template = $templates[$type];
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$template['filename']}\"",
        ];

        $callback = function () use ($template) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $template['columns'], ';');
            fputcsv($file, $template['example'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
