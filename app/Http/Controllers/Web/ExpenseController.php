<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query()
            ->with(['category', 'user'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy($request->sort ?? 'expense_date', $request->direction ?? 'desc');

        $expenses = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:expense_categories,id'],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'in:cash,bank_transfer,check,credit_card,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_billable' => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['is_billable'] = $request->boolean('is_billable');
        $validated['total'] = $validated['amount'] + ($validated['tax_amount'] ?? 0);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Dépense créée avec succès.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'approvedBy']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        if (!in_array($expense->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Cette dépense ne peut plus être modifiée.');
        }

        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        if (!in_array($expense->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Cette dépense ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:expense_categories,id'],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'in:cash,bank_transfer,check,credit_card,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_billable' => ['nullable', 'boolean'],
        ]);

        $validated['is_billable'] = $request->boolean('is_billable');
        $validated['total'] = $validated['amount'] + ($validated['tax_amount'] ?? 0);
        $validated['status'] = 'pending';

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Dépense mise à jour.');
    }

    public function destroy(Expense $expense)
    {
        if (!in_array($expense->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Cette dépense ne peut pas être supprimée.');
        }

        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée.');
    }

    public function approve(Request $request, Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Cette dépense ne peut pas être approuvée.');
        }

        $action = $request->input('action', 'approve');

        if ($action === 'approve') {
            $expense->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return back()->with('success', 'Dépense approuvée.');
        } else {
            $expense->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return back()->with('success', 'Dépense rejetée.');
        }
    }
}
