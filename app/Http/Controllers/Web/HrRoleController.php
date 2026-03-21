<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HrRole;
use App\Models\HrRoleAssignment;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Employee;
use Illuminate\Http\Request;

class HrRoleController extends Controller
{
    public function index(Request $request)
    {
        // Afficher uniquement les rôles principaux (sans parent)
        $query = HrRole::with(['direction', 'employees', 'children.assignments', 'assignments'])
            ->whereNull('parent_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_manager')) {
            $query->where('is_manager', $request->is_manager === '1');
        }

        $roles = $query->orderBy('level', 'desc')->orderBy('name')->paginate(20);
        $directions = Direction::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => HrRole::whereNull('parent_id')->count(),
            'managers' => HrRole::where('is_manager', true)->count(),
            'active' => HrRole::where('is_active', true)->count(),
        ];

        return view('hr.roles.index', compact('roles', 'directions', 'stats'));
    }

    public function create()
    {
        $directions = Direction::where('is_active', true)->orderBy('name')->get();
        $parentRoles = HrRole::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();
        return view('hr.roles.create', compact('directions', 'parentRoles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:hr_roles,id',
            'direction_id' => 'nullable|exists:directions,id',
            'level' => 'nullable|integer|min:0|max:100',
            'is_manager' => 'boolean',
            'can_approve_leaves' => 'boolean',
            'can_manage_team' => 'boolean',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_manager'] = $request->has('is_manager');
        $validated['can_approve_leaves'] = $request->has('can_approve_leaves');
        $validated['can_manage_team'] = $request->has('can_manage_team');
        $validated['is_active'] = $request->has('is_active');

        // Générer le code automatiquement
        $validated['code'] = $this->generateRoleCode($validated['name']);

        $role = HrRole::create($validated);

        if ($validated['parent_id'] ?? null) {
            return redirect()->route('hr.roles.show', $validated['parent_id'])
                ->with('success', 'Sous-rôle créé avec succès.');
        }

        return redirect()->route('hr.roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function show(HrRole $role)
    {
        $role->load(['department', 'parent', 'children.department', 'children.assignments.employee', 'employees.department', 'assignments.employee', 'assignments.department', 'assignments.reportsTo']);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $departments = Department::with('children')->where('is_active', true)->orderBy('name')->get();
        $managers = Employee::whereHas('hrRole', function($q) {
            $q->where('is_manager', true);
        })->orWhereHas('assignments.hrRole', function($q) {
            $q->where('is_manager', true);
        })->orderBy('first_name')->get();
        $parentRoles = HrRole::whereNull('parent_id')->where('id', '!=', $role->id)->orderBy('name')->get();
        
        return view('hr.roles.show', compact('role', 'employees', 'departments', 'managers', 'parentRoles'));
    }

    public function edit(HrRole $role)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('hr.roles.edit', compact('role', 'departments'));
    }

    public function update(Request $request, HrRole $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'nullable|integer|min:0|max:100',
            'is_manager' => 'boolean',
            'can_approve_leaves' => 'boolean',
            'can_manage_team' => 'boolean',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_manager'] = $request->has('is_manager');
        $validated['can_approve_leaves'] = $request->has('can_approve_leaves');
        $validated['can_manage_team'] = $request->has('can_manage_team');
        $validated['is_active'] = $request->has('is_active');

        $role->update($validated);

        return redirect()->route('hr.roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(HrRole $role)
    {
        if ($role->employees()->count() > 0 || $role->assignments()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce rôle car il est attribué à des employés.');
        }

        $role->delete();

        return redirect()->route('hr.roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }

    public function addAssignment(Request $request, HrRole $role)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'reports_to_id' => 'nullable|exists:employees,id',
            'start_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['hr_role_id'] = $role->id;
        $validated['is_active'] = true;

        HrRoleAssignment::create($validated);

        return redirect()->route('hr.roles.show', $role)
            ->with('success', 'Assignation ajoutée avec succès.');
    }

    public function removeAssignment(HrRole $role, HrRoleAssignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('hr.roles.show', $role)
            ->with('success', 'Assignation supprimée avec succès.');
    }

    private function generateRoleCode(string $name): string
    {
        // Extraire les initiales des mots
        $words = preg_split('/[\s\-_]+/', $name);
        $initials = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper(mb_substr($word, 0, 1));
            }
        }
        
        // Ajouter un numéro unique
        $count = HrRole::where('code', 'like', $initials . '-%')->count() + 1;
        $code = $initials . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // Vérifier l'unicité
        while (HrRole::where('code', $code)->exists()) {
            $count++;
            $code = $initials . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }
}
