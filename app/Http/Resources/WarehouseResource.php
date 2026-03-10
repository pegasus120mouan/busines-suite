<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'full_address' => $this->full_address,
            'phone' => $this->phone,
            'email' => $this->email,
            'manager_id' => $this->manager_id,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'total_products' => $this->total_products,
            'total_stock_value' => $this->total_stock_value,
            'manager' => $this->whenLoaded('manager', fn () => [
                'id' => $this->manager->id,
                'name' => $this->manager->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
