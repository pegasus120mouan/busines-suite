<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id;

        return [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('suppliers')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })->ignore($supplierId),
            ],
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_iban' => ['nullable', 'string', 'max:50'],
            'bank_bic' => ['nullable', 'string', 'max:20'],
            'payment_terms' => ['nullable', 'integer', 'min:0', 'max:365'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
