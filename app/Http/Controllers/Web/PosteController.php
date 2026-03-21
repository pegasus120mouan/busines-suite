<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Poste;
use App\Models\Department;
use Illuminate\Http\Request;

class PosteController extends Controller
{
    public function index(Request $request)
    {
        $query = Poste::with('department', 'employees');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $postes = $query->orderBy('name')->paginate(20);
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => Poste::count(),
            'active' => Poste::where('is_active', true)->count(),
        ];

        return view('hr.postes.index', compact('postes', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('hr.postes.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'salaire_base' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = $this->generatePosteCode($validated['name']);

        Poste::create($validated);

        return redirect()->route('hr.postes.index')
            ->with('success', 'Poste créé avec succès.');
    }

    public function edit(Poste $poste)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('hr.postes.edit', compact('poste', 'departments'));
    }

    public function update(Request $request, Poste $poste)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'salaire_base' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $poste->update($validated);

        return redirect()->route('hr.postes.index')
            ->with('success', 'Poste mis à jour avec succès.');
    }

    public function destroy(Poste $poste)
    {
        if ($poste->employees()->count() > 0) {
            return redirect()->route('hr.postes.index')
                ->with('error', 'Impossible de supprimer ce poste car il a des employés assignés.');
        }

        $poste->delete();

        return redirect()->route('hr.postes.index')
            ->with('success', 'Poste supprimé avec succès.');
    }

    private function generatePosteCode(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        $initials = substr($initials, 0, 4);
        
        if (strlen($initials) < 2) {
            $initials = strtoupper(substr($name, 0, 3));
        }

        $lastPoste = Poste::withoutGlobalScopes()
            ->where('code', 'like', $initials . '-%')
            ->orderByRaw('CAST(SUBSTRING(code, ' . (strlen($initials) + 2) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastPoste) {
            $lastNumber = (int) substr($lastPoste->code, strlen($initials) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $initials . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getByDepartment(Department $department)
    {
        $postes = Poste::where('department_id', $department->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'salaire_base']);

        return response()->json($postes);
    }
}
