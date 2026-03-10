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

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable, HasNumberSequence;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'quote_id',
        'user_id',
        'invoice_number',
        'reference',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'discount_type',
        'total',
        'amount_paid',
        'balance_due',
        'currency',
        'notes',
        'terms',
        'footer',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function getNumberField(): string
    {
        return 'invoice_number';
    }

    public function getSequenceType(): string
    {
        return 'invoice';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class)->orderBy('order');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class)->orderByDesc('created_at');
    }

    public function hasPaymentSchedule(): bool
    {
        return $this->paymentSchedules()->count() > 0;
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $taxAmount = $this->items()->sum('tax_amount');

        $discountAmount = $this->discount_type === 'percentage'
            ? ($subtotal * $this->discount_amount / 100)
            : $this->discount_amount;

        $total = $subtotal + $taxAmount - $discountAmount;
        $amountPaid = $this->payments()->where('status', 'completed')->sum('amount');

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'amount_paid' => $amountPaid,
            'balance_due' => $total - $amountPaid,
        ]);
    }

    public function updatePaymentStatus(): void
    {
        $this->calculateTotals();

        if ($this->balance_due <= 0) {
            $this->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        } elseif ($this->amount_paid > 0) {
            $this->update(['status' => 'partial']);
        }
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->balance_due > 0;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['sent', 'partial'])
            ->where('due_date', '<', now())
            ->where('balance_due', '>', 0);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0);
    }
}
