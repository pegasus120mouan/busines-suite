<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'quote_id' => $this->quote_id,
            'user_id' => $this->user_id,
            'invoice_number' => $this->invoice_number,
            'reference' => $this->reference,
            'invoice_date' => $this->invoice_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'discount_type' => $this->discount_type,
            'total' => (float) $this->total,
            'amount_paid' => (float) $this->amount_paid,
            'balance_due' => (float) $this->balance_due,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'sent_at' => $this->sent_at,
            'paid_at' => $this->paid_at,
            'is_overdue' => $this->isOverdue(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'items_count' => $this->whenCounted('items'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
