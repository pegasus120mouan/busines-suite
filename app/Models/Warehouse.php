<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
        'manager_id',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->postal_code,
            $this->country,
        ]));
    }

    public function getTotalProductsAttribute(): int
    {
        return $this->stocks()->count();
    }

    public function getTotalStockValueAttribute(): float
    {
        return $this->stocks()
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(product_stocks.quantity * products.purchase_price) as total')
            ->value('total') ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
