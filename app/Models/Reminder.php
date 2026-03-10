<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'user_id',
        'level',
        'type',
        'status',
        'scheduled_date',
        'sent_at',
        'subject',
        'message',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduledToday($query)
    {
        return $query->where('status', 'pending')
            ->whereDate('scheduled_date', '<=', now());
    }

    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    public static function getLevelLabel(int $level): string
    {
        return match ($level) {
            1 => '1ère relance',
            2 => '2ème relance',
            3 => '3ème relance (mise en demeure)',
            default => "Relance niveau $level",
        };
    }

    public static function getDefaultSubject(int $level, Invoice $invoice): string
    {
        return match ($level) {
            1 => "Rappel de paiement - Facture {$invoice->invoice_number}",
            2 => "Second rappel - Facture {$invoice->invoice_number} en attente",
            3 => "Mise en demeure - Facture {$invoice->invoice_number}",
            default => "Relance - Facture {$invoice->invoice_number}",
        };
    }

    public static function getDefaultMessage(int $level, Invoice $invoice): string
    {
        $customerName = $invoice->customer?->display_name ?? 'Client';
        $amount = number_format($invoice->balance_due, 2, ',', ' ');
        $dueDate = $invoice->due_date->format('d/m/Y');

        return match ($level) {
            1 => "Bonjour,\n\nNous vous rappelons que la facture {$invoice->invoice_number} d'un montant de {$amount} € est arrivée à échéance le {$dueDate}.\n\nNous vous remercions de bien vouloir procéder au règlement dans les meilleurs délais.\n\nCordialement",
            2 => "Bonjour,\n\nMalgré notre précédent rappel, nous constatons que la facture {$invoice->invoice_number} d'un montant de {$amount} € reste impayée.\n\nNous vous prions de régulariser cette situation dans les plus brefs délais.\n\nCordialement",
            3 => "Bonjour,\n\nLa présente constitue une mise en demeure de payer la facture {$invoice->invoice_number} d'un montant de {$amount} €.\n\nSans règlement de votre part sous 8 jours, nous serons contraints d'engager des poursuites.\n\nCordialement",
            default => "Bonjour,\n\nNous vous rappelons que la facture {$invoice->invoice_number} reste en attente de paiement.\n\nCordialement",
        };
    }
}
