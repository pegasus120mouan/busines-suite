<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'leaveType', 'approver']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->orderByDesc('created_at')->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'refused' => Leave::where('status', 'refused')->count(),
        ];

        return view('hr.leaves.index', compact('leaves', 'employees', 'leaveTypes', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('hr.leaves.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $validated['number_of_days'] = $startDate->diffInDays($endDate) + 1;
        $validated['status'] = 'pending';

        $leave = Leave::create($validated);

        return redirect()->route('hr.leaves.index')
            ->with('success', 'Demande de congé créée avec succès.');
    }

    public function show(Leave $leave)
    {
        $leave->load(['employee', 'leaveType', 'approver']);

        return view('hr.leaves.show', compact('leave'));
    }

    public function approve(Leave $leave)
    {
        $leave->approve(auth()->user());

        return redirect()->back()
            ->with('success', 'Congé approuvé avec succès.');
    }

    public function refuse(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $leave->refuse(auth()->user(), $validated['approval_notes'] ?? null);

        return redirect()->back()
            ->with('success', 'Congé refusé.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('hr.leaves.index')
            ->with('success', 'Demande de congé supprimée.');
    }
}
