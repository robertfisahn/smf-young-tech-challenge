<?php

declare(strict_types=1);



namespace App\Features\Invoices\CreateInvoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateInvoiceAction
{
    /**
     * @throws Throwable
     */
    public function execute(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Invoice
            $invoice = Invoice::create([
                'contractor_id' => $data['contractor_id'],
                'invoice_number' => $data['invoice_number'],
                'date' => $data['date'],
            ]);

            // 2. Create Items
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // 3. Create Payment
            $method = $data['payment_method'];
            $isImmediate = in_array($method, ['gotówka', 'karta']);

            $invoice->payments()->create([
                'amount' => $data['total_amount'],
                'currency' => $data['currency'],
                'method' => $method,
                'paid_at' => $isImmediate ? $invoice->date : null,
            ]);

            return $invoice;
        });
    }
}

