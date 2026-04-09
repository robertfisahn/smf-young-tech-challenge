<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'contractor_id' => 'exists:contractors,id',
            'invoice_number' => 'string|max:50|unique:invoices,invoice_number,' . $this->route('invoice')->id,
            'date' => 'date',
            'total_amount' => 'numeric|min:0',
            'currency' => 'string|max:3',
            'payment_method' => 'string|in:przelew,gotówka,karta',
        ];

        // Jeśli PUT -> wszystkie pola muszą być wysłane (required)
        // Jeśli PATCH -> pola są opcjonalne, ale jeśli są, to muszą być poprawne (sometimes)
        $prefix = $this->isMethod('PATCH') ? 'sometimes|' : 'required|';

        return collect($rules)->map(fn($rule) => $prefix . $rule)->toArray();
    }
}
