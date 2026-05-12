<?php

declare(strict_types=1);



namespace App\Features\Invoices\UpdateInvoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $invoiceId = is_object($invoice) ? $invoice->id : $invoice;

        return [
            'contractor_id' => 'required|exists:contractors,id',
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('invoices', 'invoice_number')->ignore($invoiceId),
            ],
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
}

