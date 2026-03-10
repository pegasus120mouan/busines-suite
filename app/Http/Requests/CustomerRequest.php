<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })->ignore($customerId),
            ],
            'type' => ['required', 'in:individual,company'],
            'company_name' => ['required_if:type,company', 'nullable', 'string', 'max:255'],
            'first_name' => ['required_if:type,individual', 'nullable', 'string', 'max:100'],
            'last_name' => ['required_if:type,individual', 'nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string', 'max:500'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_postal_code' => ['nullable', 'string', 'max:20'],
            'billing_country' => ['nullable', 'string', 'size:2'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'size:2'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'integer', 'min:0', 'max:365'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
