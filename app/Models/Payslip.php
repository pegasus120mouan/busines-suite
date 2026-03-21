<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'contract_id',
        'reference',
        'name',
        'date_from',
        'date_to',
        'basic_salary',
        'gross_salary',
        'net_salary',
        'total_deductions',
        'total_allowances',
        'overtime_amount',
        'bonus_amount',
        'lines',
        'currency',
        'payment_date',
        'payment_method',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'payment_date' => 'date',
        'basic_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'lines' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
            if (!$model->reference) {
                $model->reference = static::generateReference($model->tenant_id);
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public static function generateReference($tenantId): string
    {
        $lastPayslip = static::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastPayslip ? ((int) substr($lastPayslip->reference, 4) + 1) : 1;
        return 'PAY-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'confirmed' => 'Confirmé',
            'paid' => 'Payé',
            'cancelled' => 'Annulé',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'confirmed' => 'blue',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPeriodAttribute(): string
    {
        return $this->date_from->format('d/m/Y') . ' - ' . $this->date_to->format('d/m/Y');
    }

    public function calculateSalary(): void
    {
        $this->gross_salary = $this->basic_salary + $this->total_allowances + $this->overtime_amount + $this->bonus_amount;
        $this->net_salary = $this->gross_salary - $this->total_deductions;
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function markAsPaid(?string $paymentMethod = null): void
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
        ]);
    }
}
