<?php

namespace Database\Seeders;

use App\Models\NumberSequence;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class NumberSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            return;
        }

        $sequences = [
            ['type' => 'invoice', 'prefix' => 'FAC-', 'padding' => 5],
            ['type' => 'quote', 'prefix' => 'DEV-', 'padding' => 5],
            ['type' => 'payment', 'prefix' => 'PAY-', 'padding' => 5],
            ['type' => 'expense', 'prefix' => 'DEP-', 'padding' => 5],
            ['type' => 'purchase_order', 'prefix' => 'PO-', 'padding' => 5],
            ['type' => 'customer', 'prefix' => 'CLI-', 'padding' => 5],
            ['type' => 'supplier', 'prefix' => 'FOU-', 'padding' => 5],
        ];

        foreach ($sequences as $sequence) {
            NumberSequence::create([
                'tenant_id' => $tenant->id,
                ...$sequence,
                'next_number' => 1,
                'reset_yearly' => true,
                'reset_year' => date('Y'),
            ]);
        }
    }
}
