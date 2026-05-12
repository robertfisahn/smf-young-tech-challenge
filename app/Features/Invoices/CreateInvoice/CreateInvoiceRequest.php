<?php

declare(strict_types=1);



namespace App\Features\Invoices\CreateInvoice;

use Illuminate\Foundation\Http\FormRequest;

final class CreateInvoiceRequest extends FormRequest
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
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|string|in:przelew,gotówka,karta',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'contractor_id.required' => 'Wybór kontrahenta jest wymagany.',
            'invoice_number.unique' => 'Faktura o tym numerze już istnieje.',
            'items.required' => 'Faktura musi zawierać przynajmniej jedną pozycję.',
        ];
    }
}

