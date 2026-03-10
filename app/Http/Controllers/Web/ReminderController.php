<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = Reminder::with(['invoice.customer', 'user'])
            ->orderByDesc('scheduled_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $reminders = $query->paginate(20);

        // Get overdue invoices without reminders for quick action
        $overdueInvoices = Invoice::with('customer')
            ->overdue()
            ->whereDoesntHave('reminders', function ($q) {
                $q->where('status', 'pending');
            })
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        return view('reminders.index', compact('reminders', 'overdueInvoices'));
    }

    public function create(Request $request)
    {
        $invoice = null;
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with('customer')->findOrFail($request->invoice_id);
        }

        $invoices = Invoice::with('customer')
            ->unpaid()
            ->orderByDesc('due_date')
            ->get();

        $level = 1;
        if ($invoice) {
            $lastReminder = $invoice->reminders()->orderByDesc('level')->first();
            $level = $lastReminder ? $lastReminder->level + 1 : 1;
        }

        return view('reminders.create', compact('invoice', 'invoices', 'level'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'level' => 'required|integer|min:1|max:5',
            'type' => 'required|in:email,sms,letter,phone,manual',
            'scheduled_date' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        Reminder::create($validated);

        return redirect()->route('reminders.index')->with('success', 'Relance programmée avec succès.');
    }

    public function show(Reminder $reminder)
    {
        $reminder->load(['invoice.customer', 'user']);
        
        return view('reminders.show', compact('reminder'));
    }

    public function markAsSent(Reminder $reminder)
    {
        $reminder->markAsSent();

        return back()->with('success', 'Relance marquée comme envoyée.');
    }

    public function cancel(Reminder $reminder)
    {
        $reminder->update(['status' => 'cancelled']);

        return back()->with('success', 'Relance annulée.');
    }

    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
            'level' => 'required|integer|min:1|max:5',
            'type' => 'required|in:email,sms,letter,phone,manual',
            'scheduled_date' => 'required|date',
        ]);

        $count = 0;
        foreach ($validated['invoice_ids'] as $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if (!$invoice) continue;

            Reminder::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'invoice_id' => $invoiceId,
                'level' => $validated['level'],
                'type' => $validated['type'],
                'status' => 'pending',
                'scheduled_date' => $validated['scheduled_date'],
                'subject' => Reminder::getDefaultSubject($validated['level'], $invoice),
                'message' => Reminder::getDefaultMessage($validated['level'], $invoice),
            ]);
            $count++;
        }

        return redirect()->route('reminders.index')->with('success', "$count relance(s) programmée(s) avec succès.");
    }
}
