<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, Auditable;

    protected $fillable = [
        'tenant_id',
        'last_name',
        'first_name',
        'contact',
        'whatsapp',
        'sms_sent_count',
        'last_sms_sent_at',
    ];

    protected $casts = [
        'sms_sent_count' => 'integer',
        'last_sms_sent_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->last_name} {$this->first_name}");
    }

    public function getInitialsAttribute(): string
    {
        $last = mb_substr($this->last_name ?? '', 0, 1);
        $first = mb_substr($this->first_name ?? '', 0, 1);

        return mb_strtoupper($last . $first);
    }
}
