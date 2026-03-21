<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['direction', 'parent', 'employees', 'children']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $departments = $query->orderBy('name')->paginate(20);

        return view('hr.departments.index', compact('departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $directions = Direction::where('is_active', true)->orderBy('name')->get();

        return view('hr.departments.create', compact('departments', 'directions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'direction_id' => 'nullable|exists:directions,id',
            'parent_id' => 'nullable|exists:departments,id',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = $this->generateDepartmentCode($validated['name']);

        $department = Department::create($validated);

        if ($validated['parent_id'] ?? null) {
            return redirect()->route('hr.departments.show', $validated['parent_id'])
                ->with('success', 'Sous-département créé avec succès.');
        }

        return redirect()->route('hr.departments.index')
            ->with('success', 'Département créé avec succès.');
    }

    public function show(Department $department)
    {
        $department->load(['manager', 'parent', 'children', 'employees', 'jobPositions']);

        return view('hr.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $departments = Department::where('is_active', true)
            ->where('id', '!=', $department->id)
            ->orderBy('name')
            ->get();
        $managers = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('hr.departments.edit', compact('department', 'departments', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'parent_id' => 'nullable|exists:departments,id',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $department->update($validated);

        return redirect()->route('hr.departments.index')
            ->with('success', 'Département mis à jour avec succès.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('hr.departments.index')
            ->with('success', 'Département supprimé avec succès.');
    }

    private function generateDepartmentCode(string $name): string
    {
        $words = preg_split('/[\s\-_]+/', $name);
        $initials = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper(mb_substr($word, 0, 1));
            }
        }
        
        $count = Department::where('code', 'like', $initials . '-%')->count() + 1;
        $code = $initials . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        while (Department::where('code', $code)->exists()) {
            $count++;
            $code = $initials . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }

    public function getByDirection(Direction $direction)
    {
        $departments = Department::where('direction_id', $direction->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($departments);
    }
}
