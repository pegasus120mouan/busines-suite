<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Employee;
use App\Models\HrRole;
use App\Models\JobPosition;
use App\Models\Poste;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'jobPosition', 'manager']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('first_name')->paginate(20);
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'on_leave' => Employee::where('status', 'on_leave')->count(),
            'terminated' => Employee::where('status', 'terminated')->count(),
        ];

        return view('hr.employees.index', compact('employees', 'departments', 'stats'));
    }

    public function create()
    {
        $directions = Direction::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $postes = Poste::where('is_active', true)->orderBy('name')->get();
        $managers = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('hr.employees.create', compact('directions', 'departments', 'postes', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'children_count' => 'nullable|integer|min:0',
            'nationality' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'social_security_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'direction_id' => 'nullable|exists:directions,id',
            'department_id' => 'nullable|exists:departments,id',
            'poste_id' => 'nullable|exists:postes,id',
            'manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            'work_email' => 'nullable|email|max:255',
            'work_phone' => 'nullable|string|max:50',
            'work_location' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_iban' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,on_leave,terminated',
        ]);

        $employee = Employee::create($validated);

        return redirect()->route('hr.employees.show', $employee)
            ->with('success', 'Employé créé avec succès.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'jobPosition', 'manager', 'contracts', 'leaves.leaveType']);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $directions = Direction::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $postes = Poste::where('is_active', true)->orderBy('name')->get();
        $managers = Employee::where('status', 'active')
            ->where('id', '!=', $employee->id)
            ->orderBy('first_name')
            ->get();

        return view('hr.employees.edit', compact('employee', 'directions', 'departments', 'postes', 'managers'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'children_count' => 'nullable|integer|min:0',
            'nationality' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'social_security_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'direction_id' => 'nullable|exists:directions,id',
            'department_id' => 'nullable|exists:departments,id',
            'poste_id' => 'nullable|exists:postes,id',
            'manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            'departure_date' => 'nullable|date',
            'departure_reason' => 'nullable|in:resignation,termination,retirement,other',
            'work_email' => 'nullable|email|max:255',
            'work_phone' => 'nullable|string|max:50',
            'work_location' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_iban' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,on_leave,terminated',
        ]);

        $employee->update($validated);

        return redirect()->route('hr.employees.show', $employee)
            ->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employé supprimé avec succès.');
    }
}
