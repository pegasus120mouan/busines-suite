<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'due_date',
        'amount',
        'paid_amount',
        'status',
        'notes',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getBalanceAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    public function isPaid(): bool
    {
        return $this->paid_amount >= $this->amount;
    }

    public function isOverdue(): bool
    {
        return !$this->isPaid() && $this->due_date->isPast();
    }

    public function updateStatus(): void
    {
        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date->isPast()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['pending', 'partial'])
                    ->where('due_date', '<', now());
            });
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->whereIn('status', ['pending', 'partial'])
            ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }
}
