<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use App\Traits\HasNumberSequence;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable, HasNumberSequence;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'supplier_id',
        'user_id',
        'expense_number',
        'reference',
        'expense_date',
        'amount',
        'tax_amount',
        'total',
        'currency',
        'payment_method',
        'status',
        'description',
        'receipt',
        'is_recurring',
        'recurring_frequency',
        'next_recurring_date',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_recurring' => 'boolean',
        'next_recurring_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function getNumberField(): string
    {
        return 'expense_number';
    }

    public function getSequenceType(): string
    {
        return 'expense';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(int $approverId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approverId,
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }
}
