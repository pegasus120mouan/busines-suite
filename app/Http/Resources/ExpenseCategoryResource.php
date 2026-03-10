<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color,
            'icon' => $this->icon,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'total_expenses' => $this->whenLoaded('expenses', fn () => (float) $this->total_expenses),
            'parent' => new ExpenseCategoryResource($this->whenLoaded('parent')),
            'children' => ExpenseCategoryResource::collection($this->whenLoaded('children')),
            'expenses_count' => $this->whenCounted('expenses'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
