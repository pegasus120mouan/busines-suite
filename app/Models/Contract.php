<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'reference',
        'name',
        'contract_type',
        'department_id',
        'job_position_id',
        'start_date',
        'end_date',
        'trial_end_date',
        'wage',
        'wage_type',
        'currency',
        'hourly_cost',
        'hours_per_week',
        'advantages',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'trial_end_date' => 'date',
        'wage' => 'decimal:2',
        'hourly_cost' => 'decimal:2',
        'hours_per_week' => 'integer',
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
        $lastContract = static::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastContract ? ((int) substr($lastContract->reference, 4) + 1) : 1;
        return 'CTR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function getContractTypeLabelAttribute(): string
    {
        return match($this->contract_type) {
            'cdi' => 'CDI',
            'cdd' => 'CDD',
            'internship' => 'Stage',
            'freelance' => 'Freelance',
            'temporary' => 'Intérim',
            default => $this->contract_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'running' => 'En cours',
            'expired' => 'Expiré',
            'cancelled' => 'Annulé',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'running' => 'green',
            'expired' => 'amber',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isInTrialPeriod(): bool
    {
        return $this->trial_end_date && $this->trial_end_date->isFuture();
    }

    public function getDaysUntilEndAttribute(): ?int
    {
        return $this->end_date ? now()->diffInDays($this->end_date, false) : null;
    }
}
