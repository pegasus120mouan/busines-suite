<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use App\Traits\HasNumberSequence;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable, HasNumberSequence;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'user_id',
        'payment_number',
        'payment_date',
        'amount',
        'method',
        'reference',
        'notes',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function getNumberField(): string
    {
        return 'payment_number';
    }

    public function getSequenceType(): string
    {
        return 'payment';
    }

    protected static function booted(): void
    {
        static::saved(function ($payment) {
            $payment->invoice->updatePaymentStatus();
        });

        static::deleted(function ($payment) {
            $payment->invoice->updatePaymentStatus();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }
}
