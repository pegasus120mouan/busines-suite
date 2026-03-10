<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'supplier_id',
        'sku',
        'barcode',
        'name',
        'description',
        'type',
        'purchase_price',
        'selling_price',
        'tax_rate',
        'unit',
        'weight',
        'dimensions',
        'image',
        'is_active',
        'track_stock',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'reorder_quantity',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_active' => 'boolean',
        'track_stock' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stocks()->sum('quantity');
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->stocks()->sum('available_quantity');
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }
        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->total_stock <= $this->min_stock_level;
    }

    public function needsReorder(): bool
    {
        return $this->track_stock && $this->total_stock <= $this->reorder_point;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
            ->whereRaw('(SELECT COALESCE(SUM(quantity), 0) FROM product_stocks WHERE product_id = products.id) <= min_stock_level');
    }
}
