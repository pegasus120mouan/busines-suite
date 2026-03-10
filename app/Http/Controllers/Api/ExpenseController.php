<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Expense::query()
            ->with(['category', 'supplier', 'user', 'approver'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->from_date, fn ($q, $date) => $q->where('expense_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('expense_date', '<=', $date))
            ->when($request->is_recurring !== null, fn ($q) => $q->where('is_recurring', $request->boolean('is_recurring')))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return ExpenseResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(ExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'total' => $request->amount + ($request->tax_amount ?? 0),
        ]);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => new ExpenseResource($expense->load(['category', 'supplier'])),
        ], 201);
    }

    public function show(Expense $expense): ExpenseResource
    {
        return new ExpenseResource($expense->load(['category', 'supplier', 'user', 'approver']));
    }

    public function update(ExpenseRequest $request, Expense $expense): JsonResponse
    {
        $expense->update([
            ...$request->validated(),
            'total' => $request->amount + ($request->tax_amount ?? 0),
        ]);

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => new ExpenseResource($expense->load(['category', 'supplier'])),
        ]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully',
        ]);
    }

    public function approve(Expense $expense): JsonResponse
    {
        $expense->approve(auth()->id());

        return response()->json([
            'message' => 'Expense approved successfully',
            'data' => new ExpenseResource($expense),
        ]);
    }

    public function reject(Expense $expense): JsonResponse
    {
        $expense->reject();

        return response()->json([
            'message' => 'Expense rejected',
            'data' => new ExpenseResource($expense),
        ]);
    }

    public function markAsPaid(Expense $expense): JsonResponse
    {
        $expense->markAsPaid();

        return response()->json([
            'message' => 'Expense marked as paid',
            'data' => new ExpenseResource($expense),
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $query = Expense::query()
            ->when($request->from_date, fn ($q, $date) => $q->where('expense_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('expense_date', '<=', $date));

        $total = (clone $query)->sum('total');
        $pending = (clone $query)->where('status', 'pending')->sum('total');
        $approved = (clone $query)->where('status', 'approved')->sum('total');
        $paid = (clone $query)->where('status', 'paid')->sum('total');

        $byCategory = Expense::query()
            ->when($request->from_date, fn ($q, $date) => $q->where('expense_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('expense_date', '<=', $date))
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category, SUM(expenses.total) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->get();

        return response()->json([
            'data' => [
                'total' => (float) $total,
                'pending' => (float) $pending,
                'approved' => (float) $approved,
                'paid' => (float) $paid,
                'by_category' => $byCategory,
                'count' => $query->count(),
            ],
        ]);
    }
}
