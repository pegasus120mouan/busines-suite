<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    public function addStock(int $quantity): void
    {
        $this->increment('quantity', $quantity);
    }

    public function removeStock(int $quantity): void
    {
        $this->decrement('quantity', $quantity);
    }

    public function reserveStock(int $quantity): void
    {
        $this->increment('reserved_quantity', $quantity);
    }

    public function releaseReservedStock(int $quantity): void
    {
        $this->decrement('reserved_quantity', $quantity);
    }
}
