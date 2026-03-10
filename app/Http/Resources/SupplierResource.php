<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'website' => $this->website,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'full_address' => $this->full_address,
            'bank_name' => $this->bank_name,
            'bank_iban' => $this->bank_iban,
            'bank_bic' => $this->bank_bic,
            'payment_terms' => $this->payment_terms,
            'notes' => $this->notes,
            'status' => $this->status,
            'products_count' => $this->whenCounted('products'),
            'purchase_orders_count' => $this->whenCounted('purchaseOrders'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
