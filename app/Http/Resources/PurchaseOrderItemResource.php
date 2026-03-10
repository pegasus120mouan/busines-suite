<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchase_order_id,
            'product_id' => $this->product_id,
            'description' => $this->description,
            'quantity' => (float) $this->quantity,
            'quantity_received' => (float) $this->quantity_received,
            'remaining_quantity' => (float) $this->remaining_quantity,
            'unit' => $this->unit,
            'unit_price' => (float) $this->unit_price,
            'tax_rate' => (float) $this->tax_rate,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'subtotal' => (float) $this->subtotal,
            'total' => (float) $this->total,
            'sort_order' => $this->sort_order,
            'is_fully_received' => $this->isFullyReceived(),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
