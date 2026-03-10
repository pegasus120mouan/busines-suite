<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'header',
        'footer',
        'terms',
        'notes',
        'logo_position',
        'color_primary',
        'color_secondary',
        'show_logo',
        'show_payment_info',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_payment_info' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public static function getDefault(string $type): ?self
    {
        return static::where('type', $type)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public function setAsDefault(): void
    {
        static::where('tenant_id', $this->tenant_id)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
