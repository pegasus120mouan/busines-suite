<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $alerts = $this->getAlerts();
        
        return view('notifications.index', compact('alerts'));
    }

    public function getAlerts(): array
    {
        $alerts = [];

        // Factures en retard
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['sent', 'partial'])
                    ->where('due_date', '<', now());
            })
            ->with('customer')
            ->orderBy('due_date')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date);
            $alerts[] = [
                'type' => 'danger',
                'category' => 'invoice_overdue',
                'title' => "Facture en retard: {$invoice->invoice_number}",
                'message' => "Client: {$invoice->customer?->display_name} - Montant: " . number_format($invoice->balance_due, 0, ',', ' ') . " - En retard de {$daysOverdue} jours",
                'link' => route('invoices.show', $invoice),
                'date' => $invoice->due_date,
            ];
        }

        // Factures bientôt échues (dans les 7 prochains jours)
        $dueSoonInvoices = Invoice::whereIn('status', ['sent', 'partial'])
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->with('customer')
            ->orderBy('due_date')
            ->get();

        foreach ($dueSoonInvoices as $invoice) {
            $daysUntilDue = now()->diffInDays($invoice->due_date);
            $alerts[] = [
                'type' => 'warning',
                'category' => 'invoice_due_soon',
                'title' => "Facture bientôt échue: {$invoice->invoice_number}",
                'message' => "Client: {$invoice->customer?->display_name} - Échéance dans {$daysUntilDue} jour(s)",
                'link' => route('invoices.show', $invoice),
                'date' => $invoice->due_date,
            ];
        }

        // Devis expirants (dans les 7 prochains jours)
        $expiringQuotes = Quote::whereIn('status', ['draft', 'sent'])
            ->whereBetween('valid_until', [now(), now()->addDays(7)])
            ->with('customer')
            ->orderBy('valid_until')
            ->get();

        foreach ($expiringQuotes as $quote) {
            $daysUntilExpiry = now()->diffInDays($quote->valid_until);
            $alerts[] = [
                'type' => 'warning',
                'category' => 'quote_expiring',
                'title' => "Devis expire bientôt: {$quote->quote_number}",
                'message' => "Client: {$quote->customer?->display_name} - Expire dans {$daysUntilExpiry} jour(s)",
                'link' => route('quotes.show', $quote),
                'date' => $quote->valid_until,
            ];
        }

        // Devis expirés
        $expiredQuotes = Quote::whereIn('status', ['draft', 'sent'])
            ->where('valid_until', '<', now())
            ->with('customer')
            ->orderByDesc('valid_until')
            ->limit(10)
            ->get();

        foreach ($expiredQuotes as $quote) {
            $alerts[] = [
                'type' => 'danger',
                'category' => 'quote_expired',
                'title' => "Devis expiré: {$quote->quote_number}",
                'message' => "Client: {$quote->customer?->display_name} - Expiré le {$quote->valid_until->format('d/m/Y')}",
                'link' => route('quotes.show', $quote),
                'date' => $quote->valid_until,
            ];
        }

        // Produits en stock bas
        $lowStockProducts = Product::where('track_stock', true)
            ->where('is_active', true)
            ->whereHas('stocks', function ($q) {
                $q->havingRaw('SUM(quantity) <= products.min_stock_level');
            })
            ->with('stocks')
            ->get();

        foreach ($lowStockProducts as $product) {
            $totalStock = $product->stocks->sum('quantity');
            $alerts[] = [
                'type' => 'warning',
                'category' => 'low_stock',
                'title' => "Stock bas: {$product->name}",
                'message' => "Stock actuel: {$totalStock} {$product->unit} - Seuil minimum: {$product->min_stock_level}",
                'link' => route('products.show', $product),
                'date' => now(),
            ];
        }

        // Produits en rupture de stock
        $outOfStockProducts = Product::where('track_stock', true)
            ->where('is_active', true)
            ->whereDoesntHave('stocks', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->orWhereHas('stocks', function ($q) {
                $q->havingRaw('SUM(quantity) <= 0');
            })
            ->where('track_stock', true)
            ->where('is_active', true)
            ->get();

        foreach ($outOfStockProducts as $product) {
            $alerts[] = [
                'type' => 'danger',
                'category' => 'out_of_stock',
                'title' => "Rupture de stock: {$product->name}",
                'message' => "Ce produit n'a plus de stock disponible",
                'link' => route('products.show', $product),
                'date' => now(),
            ];
        }

        // Trier par type (danger en premier) puis par date
        usort($alerts, function ($a, $b) {
            $typeOrder = ['danger' => 0, 'warning' => 1, 'info' => 2];
            $typeA = $typeOrder[$a['type']] ?? 3;
            $typeB = $typeOrder[$b['type']] ?? 3;
            
            if ($typeA !== $typeB) {
                return $typeA - $typeB;
            }
            
            return $a['date'] <=> $b['date'];
        });

        return $alerts;
    }

    public function count()
    {
        $alerts = $this->getAlerts();
        $dangerCount = count(array_filter($alerts, fn($a) => $a['type'] === 'danger'));
        $warningCount = count(array_filter($alerts, fn($a) => $a['type'] === 'warning'));
        
        return response()->json([
            'total' => count($alerts),
            'danger' => $dangerCount,
            'warning' => $warningCount,
        ]);
    }
}
