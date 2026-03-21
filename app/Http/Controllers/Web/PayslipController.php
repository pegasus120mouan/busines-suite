<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Payslip;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $query = Payslip::with(['employee', 'contract']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('date_from', $date->month)
                  ->whereYear('date_from', $date->year);
        }

        $payslips = $query->orderByDesc('date_from')->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        $stats = [
            'total_this_month' => Payslip::whereMonth('date_from', now()->month)->sum('net_salary'),
            'paid' => Payslip::where('status', 'paid')->whereMonth('date_from', now()->month)->count(),
            'pending' => Payslip::whereIn('status', ['draft', 'confirmed'])->count(),
        ];

        return view('hr.payslips.index', compact('payslips', 'employees', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')
            ->whereHas('contracts', function ($q) {
                $q->where('status', 'running');
            })
            ->orderBy('first_name')
            ->get();

        return view('hr.payslips.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from',
            'basic_salary' => 'required|numeric|min:0',
            'total_allowances' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $employee = Employee::find($validated['employee_id']);
        $contract = $employee->contracts()->where('status', 'running')->first();

        $validated['contract_id'] = $contract?->id;
        $validated['name'] = 'Bulletin de paie - ' . $employee->full_name . ' - ' . Carbon::parse($validated['date_from'])->format('F Y');
        $validated['currency'] = 'XOF';
        $validated['status'] = 'draft';

        $validated['total_allowances'] = $validated['total_allowances'] ?? 0;
        $validated['overtime_amount'] = $validated['overtime_amount'] ?? 0;
        $validated['bonus_amount'] = $validated['bonus_amount'] ?? 0;
        $validated['total_deductions'] = $validated['total_deductions'] ?? 0;

        $validated['gross_salary'] = $validated['basic_salary'] + $validated['total_allowances'] + $validated['overtime_amount'] + $validated['bonus_amount'];
        $validated['net_salary'] = $validated['gross_salary'] - $validated['total_deductions'];

        $payslip = Payslip::create($validated);

        return redirect()->route('hr.payslips.show', $payslip)
            ->with('success', 'Bulletin de paie créé avec succès.');
    }

    public function show(Payslip $payslip)
    {
        $payslip->load(['employee', 'contract']);

        return view('hr.payslips.show', compact('payslip'));
    }

    public function edit(Payslip $payslip)
    {
        return view('hr.payslips.edit', compact('payslip'));
    }

    public function update(Request $request, Payslip $payslip)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'total_allowances' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['total_allowances'] = $validated['total_allowances'] ?? 0;
        $validated['overtime_amount'] = $validated['overtime_amount'] ?? 0;
        $validated['bonus_amount'] = $validated['bonus_amount'] ?? 0;
        $validated['total_deductions'] = $validated['total_deductions'] ?? 0;

        $validated['gross_salary'] = $validated['basic_salary'] + $validated['total_allowances'] + $validated['overtime_amount'] + $validated['bonus_amount'];
        $validated['net_salary'] = $validated['gross_salary'] - $validated['total_deductions'];

        $payslip->update($validated);

        return redirect()->route('hr.payslips.show', $payslip)
            ->with('success', 'Bulletin de paie mis à jour.');
    }

    public function confirm(Payslip $payslip)
    {
        $payslip->confirm();

        return redirect()->back()->with('success', 'Bulletin de paie confirmé.');
    }

    public function pay(Request $request, Payslip $payslip)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:255',
        ]);

        $payslip->markAsPaid($validated['payment_method'] ?? null);

        return redirect()->back()->with('success', 'Bulletin de paie marqué comme payé.');
    }

    public function destroy(Payslip $payslip)
    {
        $payslip->delete();

        return redirect()->route('hr.payslips.index')
            ->with('success', 'Bulletin de paie supprimé.');
    }
}
