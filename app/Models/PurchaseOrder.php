<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use App\Traits\HasNumberSequence;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable, HasNumberSequence;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'warehouse_id',
        'user_id',
        'order_number',
        'reference',
        'order_date',
        'expected_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'total',
        'currency',
        'notes',
        'terms',
        'sent_at',
        'confirmed_at',
        'received_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function getNumberField(): string
    {
        return 'order_number';
    }

    public function getSequenceType(): string
    {
        return 'purchase_order';
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)->orderBy('sort_order');
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $taxAmount = $this->items()->sum('tax_amount');

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount - $this->discount_amount + $this->shipping_cost,
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsReceived(): void
    {
        $this->update([
            'status' => 'received',
            'received_at' => now(),
        ]);
    }

    public function isFullyReceived(): bool
    {
        return $this->items->every(fn ($item) => $item->quantity_received >= $item->quantity);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['sent', 'confirmed']);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }
}
