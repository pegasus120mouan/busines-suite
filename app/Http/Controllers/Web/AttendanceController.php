<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderByDesc('date')->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        $today = Carbon::today();
        $stats = [
            'present_today' => Attendance::whereDate('date', $today)->where('status', 'present')->count(),
            'absent_today' => Attendance::whereDate('date', $today)->where('status', 'absent')->count(),
            'late_today' => Attendance::whereDate('date', $today)->where('status', 'late')->count(),
        ];

        return view('hr.attendances.index', compact('attendances', 'employees', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('hr.attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,half_day,holiday,weekend',
            'notes' => 'nullable|string',
        ]);

        if ($validated['check_in'] && $validated['check_out']) {
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $validated['worked_hours'] = $checkOut->diffInMinutes($checkIn) / 60;
        }

        $attendance = Attendance::create($validated);

        return redirect()->route('hr.attendances.index')
            ->with('success', 'Pointage enregistré avec succès.');
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            return redirect()->back()->with('error', 'Pointage déjà effectué pour aujourd\'hui.');
        }

        $now = Carbon::now();
        $status = $now->hour >= 9 ? 'late' : 'present';

        Attendance::create([
            'employee_id' => $validated['employee_id'],
            'date' => $today,
            'check_in' => $now->format('H:i'),
            'status' => $status,
        ]);

        return redirect()->back()->with('success', 'Pointage d\'entrée enregistré.');
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Aucun pointage d\'entrée trouvé pour aujourd\'hui.');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Pointage de sortie déjà effectué.');
        }

        $now = Carbon::now();
        $checkIn = Carbon::parse($attendance->check_in);
        $workedHours = $now->diffInMinutes($checkIn) / 60;

        $attendance->update([
            'check_out' => $now->format('H:i'),
            'worked_hours' => $workedHours,
        ]);

        return redirect()->back()->with('success', 'Pointage de sortie enregistré.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('hr.attendances.index')
            ->with('success', 'Pointage supprimé.');
    }
}
