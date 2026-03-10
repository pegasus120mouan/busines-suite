<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use App\Traits\HasNumberSequence;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable, HasNumberSequence;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'user_id',
        'quote_number',
        'reference',
        'quote_date',
        'valid_until',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'discount_type',
        'total',
        'currency',
        'notes',
        'terms',
        'footer',
        'sent_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function getNumberField(): string
    {
        return 'quote_number';
    }

    public function getSequenceType(): string
    {
        return 'quote';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('sort_order');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $taxAmount = $this->items()->sum('tax_amount');

        $discountAmount = $this->discount_type === 'percentage'
            ? ($subtotal * $this->discount_amount / 100)
            : $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount - $discountAmount,
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function markAsRejected(): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->valid_until->isPast() && $this->status === 'sent';
    }

    public function canBeConverted(): bool
    {
        return $this->status === 'accepted' && !$this->invoice;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'sent')->where('valid_until', '<', now());
    }
}
