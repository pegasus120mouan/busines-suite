<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'user_id' => $this->user_id,
            'expense_number' => $this->expense_number,
            'reference' => $this->reference,
            'expense_date' => $this->expense_date->format('Y-m-d'),
            'amount' => (float) $this->amount,
            'tax_amount' => (float) $this->tax_amount,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'description' => $this->description,
            'receipt' => $this->receipt,
            'is_recurring' => $this->is_recurring,
            'recurring_frequency' => $this->recurring_frequency,
            'next_recurring_date' => $this->next_recurring_date?->format('Y-m-d'),
            'approved_at' => $this->approved_at,
            'category' => new ExpenseCategoryResource($this->whenLoaded('category')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'approver' => $this->whenLoaded('approver', fn () => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
