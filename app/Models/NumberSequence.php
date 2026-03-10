<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'type',
        'prefix',
        'suffix',
        'next_number',
        'padding',
        'reset_yearly',
        'reset_year',
    ];

    protected $casts = [
        'next_number' => 'integer',
        'padding' => 'integer',
        'reset_yearly' => 'boolean',
        'reset_year' => 'integer',
    ];
}
