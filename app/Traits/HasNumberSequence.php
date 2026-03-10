<?php

namespace App\Traits;

use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;

trait HasNumberSequence
{
    protected static function bootHasNumberSequence(): void
    {
        static::creating(function ($model) {
            $numberField = $model->getNumberField();
            $sequenceType = $model->getSequenceType();

            if (empty($model->{$numberField}) && auth()->check() && auth()->user()->tenant_id) {
                $model->{$numberField} = static::generateNextNumber(auth()->user()->tenant_id, $sequenceType);
            }
        });
    }

    public static function generateNextNumber(string $tenantId, string $type): string
    {
        return DB::transaction(function () use ($tenantId, $type) {
            $sequence = NumberSequence::lockForUpdate()
                ->where('tenant_id', $tenantId)
                ->where('type', $type)
                ->first();

            if (!$sequence) {
                $sequence = NumberSequence::create([
                    'tenant_id' => $tenantId,
                    'type' => $type,
                    'prefix' => strtoupper(substr($type, 0, 3)) . '-',
                    'next_number' => 1,
                    'padding' => 5,
                ]);
            }

            $currentYear = date('Y');
            if ($sequence->reset_yearly && $sequence->reset_year !== (int) $currentYear) {
                $sequence->next_number = 1;
                $sequence->reset_year = $currentYear;
            }

            $number = $sequence->prefix . str_pad($sequence->next_number, $sequence->padding, '0', STR_PAD_LEFT) . $sequence->suffix;

            $sequence->increment('next_number');

            return $number;
        });
    }

    abstract public function getNumberField(): string;

    abstract public function getSequenceType(): string;
}
