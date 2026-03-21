<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'birth_date',
        'birth_place',
        'gender',
        'marital_status',
        'children_count',
        'nationality',
        'id_number',
        'social_security_number',
        'address',
        'city',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'direction_id',
        'department_id',
        'poste_id',
        'manager_id',
        'coach_id',
        'hire_date',
        'departure_date',
        'departure_reason',
        'work_email',
        'work_phone',
        'work_location',
        'bank_name',
        'bank_account_number',
        'bank_iban',
        'photo',
        'notes',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'departure_date' => 'date',
        'children_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
            if (!$model->employee_number) {
                $model->employee_number = static::generateEmployeeNumber($model->tenant_id);
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public static function generateEmployeeNumber($tenantId): string
    {
        $lastEmployee = static::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastEmployee ? ((int) substr($lastEmployee->employee_number, 3) + 1) : 1;
        return 'EMP' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function hrRole(): BelongsTo
    {
        return $this->belongsTo(HrRole::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(HrRoleAssignment::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'coach_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function currentContract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'id', 'employee_id')
            ->where('status', 'running')
            ->latest('start_date');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function getSeniorityAttribute(): ?string
    {
        if (!$this->hire_date) {
            return null;
        }
        $years = $this->hire_date->diffInYears(now());
        $months = $this->hire_date->diffInMonths(now()) % 12;
        
        if ($years > 0) {
            return $years . ' an' . ($years > 1 ? 's' : '') . ($months > 0 ? ' et ' . $months . ' mois' : '');
        }
        return $months . ' mois';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'on_leave' => 'En congé',
            'terminated' => 'Parti',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'on_leave' => 'amber',
            'terminated' => 'red',
            default => 'gray',
        };
    }
}
