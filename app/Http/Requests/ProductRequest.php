<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })->ignore($productId),
            ],
            'barcode' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'in:product,service'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'unit' => ['nullable', 'string', 'max:20'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'track_stock' => ['nullable', 'boolean'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'max_stock_level' => ['nullable', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
            'reorder_quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
