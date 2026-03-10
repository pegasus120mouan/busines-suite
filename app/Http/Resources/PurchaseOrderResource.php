<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplier_id,
            'warehouse_id' => $this->warehouse_id,
            'user_id' => $this->user_id,
            'order_number' => $this->order_number,
            'reference' => $this->reference,
            'order_date' => $this->order_date->format('Y-m-d'),
            'expected_date' => $this->expected_date?->format('Y-m-d'),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'shipping_cost' => (float) $this->shipping_cost,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'sent_at' => $this->sent_at,
            'confirmed_at' => $this->confirmed_at,
            'received_at' => $this->received_at,
            'is_fully_received' => $this->isFullyReceived(),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'items' => PurchaseOrderItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenCounted('items'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
