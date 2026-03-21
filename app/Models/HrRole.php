<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrRole extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'code',
        'description',
        'department_id',
        'direction_id',
        'level',
        'is_manager',
        'can_approve_leaves',
        'can_manage_team',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_manager' => 'boolean',
        'can_approve_leaves' => 'boolean',
        'can_manage_team' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(HrRole::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(HrRole::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->hasMany(HrRole::class, 'parent_id')->with('allChildren');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name . ' - ' . $this->name;
        }
        return $this->name;
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'hr_role_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(HrRoleAssignment::class);
    }

    public function getEmployeesCountAttribute(): int
    {
        return $this->employees()->count();
    }

    public function getAssignmentsCountAttribute(): int
    {
        return $this->assignments()->where('is_active', true)->count();
    }
}
