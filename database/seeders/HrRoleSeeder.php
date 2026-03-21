<?php

namespace Database\Seeders;

use App\Models\HrRole;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class HrRoleSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $roles = [
            [
                'name' => 'Directeur',
                'code' => 'DIR',
                'description' => 'Directeur de département ou de division',
                'level' => 100,
                'is_manager' => true,
                'can_approve_leaves' => true,
                'can_manage_team' => true,
                'color' => '#7C3AED',
            ],
            [
                'name' => 'Manager',
                'code' => 'MGR',
                'description' => 'Manager d\'équipe avec responsabilités d\'encadrement',
                'level' => 80,
                'is_manager' => true,
                'can_approve_leaves' => true,
                'can_manage_team' => true,
                'color' => '#2563EB',
            ],
            [
                'name' => 'Chef d\'équipe',
                'code' => 'LEAD',
                'description' => 'Chef d\'équipe ou team lead',
                'level' => 60,
                'is_manager' => true,
                'can_approve_leaves' => true,
                'can_manage_team' => true,
                'color' => '#0891B2',
            ],
            [
                'name' => 'Senior',
                'code' => 'SR',
                'description' => 'Employé senior avec expérience confirmée',
                'level' => 40,
                'is_manager' => false,
                'can_approve_leaves' => false,
                'can_manage_team' => false,
                'color' => '#059669',
            ],
            [
                'name' => 'Employé',
                'code' => 'EMP',
                'description' => 'Employé standard',
                'level' => 20,
                'is_manager' => false,
                'can_approve_leaves' => false,
                'can_manage_team' => false,
                'color' => '#6B7280',
            ],
            [
                'name' => 'Stagiaire',
                'code' => 'STG',
                'description' => 'Stagiaire ou apprenti',
                'level' => 10,
                'is_manager' => false,
                'can_approve_leaves' => false,
                'can_manage_team' => false,
                'color' => '#F59E0B',
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($roles as $roleData) {
                HrRole::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'code' => $roleData['code'],
                    ],
                    array_merge($roleData, [
                        'tenant_id' => $tenant->id,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
