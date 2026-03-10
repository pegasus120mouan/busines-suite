<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'discount_type',
        'subtotal',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function ($item) {
            $item->calculateTotals();
        });

        static::saved(function ($item) {
            $item->invoice->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->invoice->calculateTotals();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotals(): void
    {
        $lineTotal = $this->quantity * $this->unit_price;

        $discount = $this->discount_type === 'percentage'
            ? ($lineTotal * $this->discount_amount / 100)
            : $this->discount_amount;

        $this->subtotal = $lineTotal - $discount;
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount;
    }
}
