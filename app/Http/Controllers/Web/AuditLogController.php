<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable'])
            ->orderByDesc('created_at');

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->where('auditable_type', 'App\\Models\\' . $request->model);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $users = User::orderBy('name')->get();

        $modelTypes = [
            'Customer' => 'Clients',
            'Invoice' => 'Factures',
            'Quote' => 'Devis',
            'Product' => 'Produits',
            'Expense' => 'Dépenses',
            'Payment' => 'Paiements',
            'Supplier' => 'Fournisseurs',
            'User' => 'Utilisateurs',
        ];

        return view('audit-logs.index', compact('logs', 'users', 'modelTypes'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'auditable']);
        
        return view('audit-logs.show', compact('auditLog'));
    }
}
