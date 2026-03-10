<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    public function create(array $data): Quote
    {
        return DB::transaction(function () use ($data) {
            $quote = Quote::create([
                'customer_id' => $data['customer_id'],
                'user_id' => auth()->id(),
                'reference' => $data['reference'] ?? null,
                'quote_date' => $data['quote_date'],
                'valid_until' => $data['valid_until'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'discount_type' => $data['discount_type'] ?? 'fixed',
                'currency' => $data['currency'] ?? 'EUR',
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'footer' => $data['footer'] ?? null,
                'status' => 'draft',
            ]);

            $this->syncItems($quote, $data['items']);

            return $quote->fresh();
        });
    }

    public function update(Quote $quote, array $data): Quote
    {
        return DB::transaction(function () use ($quote, $data) {
            $quote->update([
                'customer_id' => $data['customer_id'],
                'reference' => $data['reference'] ?? null,
                'quote_date' => $data['quote_date'],
                'valid_until' => $data['valid_until'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'discount_type' => $data['discount_type'] ?? 'fixed',
                'currency' => $data['currency'] ?? $quote->currency,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'footer' => $data['footer'] ?? null,
            ]);

            $this->syncItems($quote, $data['items']);

            return $quote->fresh();
        });
    }

    protected function syncItems(Quote $quote, array $items): void
    {
        $quote->items()->delete();

        foreach ($items as $index => $item) {
            QuoteItem::create([
                'quote_id' => $quote->id,
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
}
