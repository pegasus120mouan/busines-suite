<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobPosition;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with(['employee', 'department', 'jobPosition']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->orderByDesc('start_date')->paginate(20);
        $employees = Employee::orderBy('first_name')->get();

        $stats = [
            'total' => Contract::count(),
            'running' => Contract::where('status', 'running')->count(),
            'expiring_soon' => Contract::where('status', 'running')
                ->whereNotNull('end_date')
                ->where('end_date', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('hr.contracts.index', compact('contracts', 'employees', 'stats'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $jobPositions = JobPosition::where('is_active', true)->orderBy('name')->get();
        $selectedEmployee = $request->employee_id ? Employee::find($request->employee_id) : null;

        return view('hr.contracts.create', compact('employees', 'departments', 'jobPositions', 'selectedEmployee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'contract_type' => 'required|in:cdi,cdd,internship,freelance,temporary',
            'department_id' => 'nullable|exists:departments,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'trial_end_date' => 'nullable|date|after:start_date',
            'wage' => 'required|numeric|min:0',
            'wage_type' => 'required|in:monthly,hourly,daily,yearly',
            'currency' => 'nullable|string|max:3',
            'hours_per_week' => 'nullable|integer|min:1|max:168',
            'advantages' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'draft';

        $contract = Contract::create($validated);

        return redirect()->route('hr.contracts.show', $contract)
            ->with('success', 'Contrat créé avec succès.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['employee', 'department', 'jobPosition']);

        return view('hr.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $employees = Employee::orderBy('first_name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $jobPositions = JobPosition::where('is_active', true)->orderBy('name')->get();

        return view('hr.contracts.edit', compact('contract', 'employees', 'departments', 'jobPositions'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contract_type' => 'required|in:cdi,cdd,internship,freelance,temporary',
            'department_id' => 'nullable|exists:departments,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'trial_end_date' => 'nullable|date|after:start_date',
            'wage' => 'required|numeric|min:0',
            'wage_type' => 'required|in:monthly,hourly,daily,yearly',
            'currency' => 'nullable|string|max:3',
            'hours_per_week' => 'nullable|integer|min:1|max:168',
            'advantages' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,running,expired,cancelled',
        ]);

        $contract->update($validated);

        return redirect()->route('hr.contracts.show', $contract)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    public function activate(Contract $contract)
    {
        $contract->update(['status' => 'running']);

        return redirect()->back()
            ->with('success', 'Contrat activé avec succès.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('hr.contracts.index')
            ->with('success', 'Contrat supprimé avec succès.');
    }
}
