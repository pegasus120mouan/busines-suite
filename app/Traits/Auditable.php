<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (!empty($dirty)) {
                $original = array_intersect_key($model->getOriginal(), $dirty);
                $model->logAudit('updated', $original, $dirty);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getOriginal(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logAudit('restored', null, $model->getAttributes());
            });
        }
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    protected function logAudit(string $event, ?array $oldValues, ?array $newValues): void
    {
        if (!auth()->check()) {
            return;
        }

        $tenantId = auth()->user()->tenant_id ?? ($this->tenant_id ?? null);
        
        if (!$tenantId) {
            return;
        }

        AuditLog::create([
            'tenant_id' => $tenantId,
            'user_id' => auth()->id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }
}
