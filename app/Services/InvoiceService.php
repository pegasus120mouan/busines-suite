<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'user_id' => auth()->id(),
                'reference' => $data['reference'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'discount_type' => $data['discount_type'] ?? 'fixed',
                'currency' => $data['currency'] ?? 'EUR',
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'footer' => $data['footer'] ?? null,
                'status' => 'draft',
            ]);

            $this->syncItems($invoice, $data['items']);

            return $invoice->fresh();
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'customer_id' => $data['customer_id'],
                'reference' => $data['reference'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'discount_type' => $data['discount_type'] ?? 'fixed',
                'currency' => $data['currency'] ?? $invoice->currency,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'footer' => $data['footer'] ?? null,
            ]);

            $this->syncItems($invoice, $data['items']);

            return $invoice->fresh();
        });
    }

    public function createFromQuote(Quote $quote): Invoice
    {
        return DB::transaction(function () use ($quote) {
            $invoice = Invoice::create([
                'customer_id' => $quote->customer_id,
                'quote_id' => $quote->id,
                'user_id' => auth()->id(),
                'reference' => $quote->reference,
                'invoice_date' => now(),
                'due_date' => now()->addDays($quote->customer->payment_terms ?? 30),
                'discount_amount' => $quote->discount_amount,
                'discount_type' => $quote->discount_type,
                'currency' => $quote->currency,
                'notes' => $quote->notes,
                'terms' => $quote->terms,
                'footer' => $quote->footer,
                'status' => 'draft',
            ]);

            foreach ($quote->items as $quoteItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $quoteItem->product_id,
                    'description' => $quoteItem->description,
                    'quantity' => $quoteItem->quantity,
                    'unit' => $quoteItem->unit,
                    'unit_price' => $quoteItem->unit_price,
                    'tax_rate' => $quoteItem->tax_rate,
                    'discount_amount' => $quoteItem->discount_amount,
                    'discount_type' => $quoteItem->discount_type,
                    'sort_order' => $quoteItem->sort_order,
                ]);
            }

            $quote->update(['status' => 'converted']);

            return $invoice->fresh();
        });
    }

    protected function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();

        foreach ($items as $index => $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'unit',
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 20,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'discount_type' => $item['discount_type'] ?? 'fixed',
                'sort_order' => $index,
            ]);
        }
    }

    public function getStatistics(?string $fromDate = null, ?string $toDate = null): array
    {
        $query = Invoice::query();

        if ($fromDate) {
            $query->where('invoice_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('invoice_date', '<=', $toDate);
        }

        $total = (clone $query)->sum('total');
        $paid = (clone $query)->where('status', 'paid')->sum('total');
        $pending = (clone $query)->whereIn('status', ['sent', 'partial'])->sum('balance_due');
        $overdue = (clone $query)->overdue()->sum('balance_due');

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'total_invoiced' => (float) $total,
            'total_paid' => (float) $paid,
            'total_pending' => (float) $pending,
            'total_overdue' => (float) $overdue,
            'by_status' => $byStatus,
            'count' => $query->count(),
        ];
    }
}
