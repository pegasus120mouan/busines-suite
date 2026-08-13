<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Direction::withCount('departments');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $directions = $query->orderBy('name')->paginate(20);

        $stats = [
            'total' => Direction::count(),
            'active' => Direction::where('is_active', true)->count(),
        ];

        return view('hr.directions.index', compact('directions', 'stats'));
    }

    public function create()
    {
        return view('hr.directions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = $this->generateDirectionCode($validated['name']);

        Direction::create($validated);

        return redirect()->route('hr.directions.index')
            ->with('success', 'Direction créée avec succès.');
    }

    public function show(Direction $direction)
    {
        $direction->load(['departments.employees', 'departments.children']);

        return view('hr.directions.show', compact('direction'));
    }

    public function edit(Direction $direction)
    {
        return view('hr.directions.edit', compact('direction'));
    }

    public function update(Request $request, Direction $direction)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $direction->update($validated);

        return redirect()->route('hr.directions.index')
            ->with('success', 'Direction mise à jour avec succès.');
    }

    public function destroy(Direction $direction)
    {
        if ($direction->departments()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette direction car elle contient des départements.');
        }

        $direction->delete();

        return redirect()->route('hr.directions.index')
            ->with('success', 'Direction supprimée avec succès.');
    }

    private function generateDirectionCode(string $name): string
    {
        $words = preg_split('/[\s\-_]+/', $name);
        $initials = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper(mb_substr($word, 0, 1));
            }
        }

        $tenantId = auth()->user()->tenant_id;

        $count = Direction::withoutGlobalScopes()
            ->withTrashed()
            ->where('tenant_id', $tenantId)
            ->where('code', 'like', $initials . '-%')
            ->count() + 1;

        $code = $initials . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (Direction::withoutGlobalScopes()
            ->withTrashed()
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->exists()) {
            $count++;
            $code = $initials . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
