<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contractor_id' => 'required|exists:contractors,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
            'date' => 'required|date',
            'total_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_method' => 'required|string|in:przelew,gotówka,karta',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
