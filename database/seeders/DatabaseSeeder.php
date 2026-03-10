<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Create demo tenant
        $tenant = Tenant::create([
            'name' => 'Demo Company',
            'slug' => 'demo-company',
            'email' => 'admin@demo.com',
            'currency' => 'EUR',
            'timezone' => 'Europe/Paris',
            'locale' => 'fr',
            'status' => 'active',
            'plan' => 'professional',
            'trial_ends_at' => now()->addDays(30),
        ]);

        // Create admin user
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Manager User',
            'email' => 'manager@demo.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $manager->assignRole('manager');

        // Run tenant-dependent seeders
        $this->call([
            TaxRateSeeder::class,
            ExpenseCategorySeeder::class,
            NumberSequenceSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
