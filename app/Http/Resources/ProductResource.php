<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'purchase_price' => (float) $this->purchase_price,
            'selling_price' => (float) $this->selling_price,
            'tax_rate' => (float) $this->tax_rate,
            'unit' => $this->unit,
            'weight' => $this->weight ? (float) $this->weight : null,
            'dimensions' => $this->dimensions,
            'image' => $this->image,
            'is_active' => $this->is_active,
            'track_stock' => $this->track_stock,
            'min_stock_level' => $this->min_stock_level,
            'max_stock_level' => $this->max_stock_level,
            'reorder_point' => $this->reorder_point,
            'reorder_quantity' => $this->reorder_quantity,
            'total_stock' => $this->stocks_sum_quantity ?? $this->total_stock,
            'available_stock' => $this->available_stock,
            'profit_margin' => round($this->profit_margin, 2),
            'is_low_stock' => $this->isLowStock(),
            'needs_reorder' => $this->needsReorder(),
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'stocks' => ProductStockResource::collection($this->whenLoaded('stocks')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
