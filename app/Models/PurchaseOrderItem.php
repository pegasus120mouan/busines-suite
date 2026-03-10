<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'description',
        'quantity',
        'quantity_received',
        'unit',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'subtotal',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'quantity_received' => 'decimal:3',
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
            $item->purchaseOrder->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->purchaseOrder->calculateTotals();
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotals(): void
    {
        $lineTotal = $this->quantity * $this->unit_price;
        $this->subtotal = $lineTotal - $this->discount_amount;
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount;
    }

    public function getRemainingQuantityAttribute(): float
    {
        return $this->quantity - $this->quantity_received;
    }

    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity;
    }
}
