<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            return;
        }

        $categories = [
            ['name' => 'Salaires et charges', 'color' => '#3B82F6', 'icon' => 'users'],
            ['name' => 'Loyer et charges locatives', 'color' => '#10B981', 'icon' => 'building'],
            ['name' => 'Fournitures de bureau', 'color' => '#F59E0B', 'icon' => 'clipboard'],
            ['name' => 'Télécommunications', 'color' => '#8B5CF6', 'icon' => 'phone'],
            ['name' => 'Déplacements et transport', 'color' => '#EF4444', 'icon' => 'car'],
            ['name' => 'Marketing et publicité', 'color' => '#EC4899', 'icon' => 'megaphone'],
            ['name' => 'Assurances', 'color' => '#06B6D4', 'icon' => 'shield'],
            ['name' => 'Services professionnels', 'color' => '#84CC16', 'icon' => 'briefcase'],
            ['name' => 'Équipements et matériel', 'color' => '#F97316', 'icon' => 'wrench'],
            ['name' => 'Frais bancaires', 'color' => '#6366F1', 'icon' => 'credit-card'],
            ['name' => 'Impôts et taxes', 'color' => '#14B8A6', 'icon' => 'receipt'],
            ['name' => 'Autres dépenses', 'color' => '#78716C', 'icon' => 'folder'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create([
                'tenant_id' => $tenant->id,
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'color' => $category['color'],
                'icon' => $category['icon'],
                'is_active' => true,
            ]);
        }
    }
}
