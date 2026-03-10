<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'company_name' => $this->company_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'website' => $this->website,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'billing_address' => $this->billing_address,
            'billing_city' => $this->billing_city,
            'billing_postal_code' => $this->billing_postal_code,
            'billing_country' => $this->billing_country,
            'shipping_address' => $this->shipping_address,
            'shipping_city' => $this->shipping_city,
            'shipping_postal_code' => $this->shipping_postal_code,
            'shipping_country' => $this->shipping_country,
            'full_address' => $this->full_address,
            'credit_limit' => (float) $this->credit_limit,
            'payment_terms' => $this->payment_terms,
            'notes' => $this->notes,
            'status' => $this->status,
            'total_invoiced' => $this->whenLoaded('invoices', fn () => (float) $this->total_invoiced),
            'total_paid' => $this->whenLoaded('invoices', fn () => (float) $this->total_paid),
            'outstanding_balance' => $this->whenLoaded('invoices', fn () => (float) $this->outstanding_balance),
            'invoices_count' => $this->whenCounted('invoices'),
            'quotes_count' => $this->whenCounted('quotes'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
