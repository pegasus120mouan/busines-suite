<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'payment_method' => ['required', 'in:cash,bank_transfer,check,credit_card,paypal,other'],
            'description' => ['nullable', 'string', 'max:1000'],
            'receipt' => ['nullable', 'string', 'max:255'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurring_frequency' => ['nullable', 'required_if:is_recurring,true', 'in:daily,weekly,monthly,quarterly,yearly'],
            'next_recurring_date' => ['nullable', 'required_if:is_recurring,true', 'date'],
        ];
    }
}
