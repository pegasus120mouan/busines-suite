<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'quote_number' => $this->quote_number,
            'reference' => $this->reference,
            'quote_date' => $this->quote_date->format('Y-m-d'),
            'valid_until' => $this->valid_until->format('Y-m-d'),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'discount_type' => $this->discount_type,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'sent_at' => $this->sent_at,
            'accepted_at' => $this->accepted_at,
            'rejected_at' => $this->rejected_at,
            'is_expired' => $this->isExpired(),
            'can_be_converted' => $this->canBeConverted(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'items' => QuoteItemResource::collection($this->whenLoaded('items')),
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'items_count' => $this->whenCounted('items'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
