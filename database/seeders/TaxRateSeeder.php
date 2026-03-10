<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            return;
        }

        $taxRates = [
            ['name' => 'TVA 20%', 'rate' => 20.00, 'code' => 'TVA20', 'is_default' => true],
            ['name' => 'TVA 10%', 'rate' => 10.00, 'code' => 'TVA10', 'is_default' => false],
            ['name' => 'TVA 5.5%', 'rate' => 5.50, 'code' => 'TVA55', 'is_default' => false],
            ['name' => 'TVA 2.1%', 'rate' => 2.10, 'code' => 'TVA21', 'is_default' => false],
            ['name' => 'Exonéré', 'rate' => 0.00, 'code' => 'EXONERE', 'is_default' => false],
        ];

        foreach ($taxRates as $taxRate) {
            TaxRate::create([
                'tenant_id' => $tenant->id,
                ...$taxRate,
                'is_active' => true,
            ]);
        }
    }
}
