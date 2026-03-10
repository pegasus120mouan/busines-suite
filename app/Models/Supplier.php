<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable;

    protected $fillable = [
        'tenant_id',
        'code',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'mobile',
        'website',
        'tax_number',
        'registration_number',
        'address',
        'city',
        'postal_code',
        'country',
        'bank_name',
        'bank_iban',
        'bank_bic',
        'payment_terms',
        'notes',
        'status',
    ];

    protected $casts = [
        'payment_terms' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->postal_code,
            $this->country,
        ]));
    }
}
