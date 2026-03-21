<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLeaveTypes = [
            [
                'name' => 'Congé annuel',
                'code' => 'CA',
                'color' => '#3B82F6',
                'default_days' => 30,
                'requires_approval' => true,
                'is_paid' => true,
                'description' => 'Congé annuel payé',
            ],
            [
                'name' => 'Congé maladie',
                'code' => 'CM',
                'color' => '#EF4444',
                'default_days' => 15,
                'requires_approval' => true,
                'is_paid' => true,
                'description' => 'Congé pour raison de santé',
            ],
            [
                'name' => 'Congé maternité',
                'code' => 'CMAT',
                'color' => '#EC4899',
                'default_days' => 98,
                'requires_approval' => true,
                'is_paid' => true,
                'description' => 'Congé maternité légal',
            ],
            [
                'name' => 'Congé paternité',
                'code' => 'CPAT',
                'color' => '#8B5CF6',
                'default_days' => 10,
                'requires_approval' => true,
                'is_paid' => true,
                'description' => 'Congé paternité légal',
            ],
            [
                'name' => 'Congé sans solde',
                'code' => 'CSS',
                'color' => '#6B7280',
                'default_days' => 0,
                'requires_approval' => true,
                'is_paid' => false,
                'description' => 'Congé non rémunéré',
            ],
            [
                'name' => 'Congé exceptionnel',
                'code' => 'CE',
                'color' => '#F59E0B',
                'default_days' => 3,
                'requires_approval' => true,
                'is_paid' => true,
                'description' => 'Mariage, décès, naissance...',
            ],
            [
                'name' => 'RTT',
                'code' => 'RTT',
                'color' => '#10B981',
                'default_days' => 12,
                'requires_approval' => true,
                'is_paid' => true,
                'description' => 'Réduction du temps de travail',
            ],
        ];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($defaultLeaveTypes as $leaveType) {
                LeaveType::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'code' => $leaveType['code'],
                    ],
                    array_merge($leaveType, ['tenant_id' => $tenant->id])
                );
            }
        }
    }
}
