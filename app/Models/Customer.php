<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable;

    protected $fillable = [
        'tenant_id',
        'code',
        'type',
        'company_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'website',
        'tax_number',
        'registration_number',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'credit_limit',
        'payment_terms',
        'notes',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms' => 'integer',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'company') {
            return $this->company_name ?? '';
        }
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->billing_address,
            $this->billing_city,
            $this->billing_postal_code,
            $this->billing_country,
        ]));
    }

    public function getTotalInvoicedAttribute(): float
    {
        return $this->invoices()->sum('total');
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->invoices()->sum('amount_paid');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return $this->invoices()->sum('balance_due');
    }
}
