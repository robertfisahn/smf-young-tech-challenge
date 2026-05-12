<?php

declare(strict_types=1);



namespace App\Features\Invoices\UpdateInvoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateInvoiceAction
{
    /**
     * @throws Throwable
     */
    public function execute(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            // 1. Update Invoice Basic Data
            $invoice->update([
                'contractor_id' => $data['contractor_id'],
                'invoice_number' => $data['invoice_number'],
                'date' => $data['date'],
            ]);

            // 2. Sync Items (Simple approach: delete and recreate)
            $invoice->items()->delete();
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // 3. Sync Payment
            $method = $data['payment_method'];
            $isImmediate = in_array($method, ['gotówka', 'karta']);

            $payment = $invoice->payments()->first();
            
            $paymentData = [
                'amount' => $data['total_amount'],
                'currency' => $data['currency'],
                'method' => $method,
                'paid_at' => $isImmediate ? $invoice->date : null,
            ];

            if ($payment) {
                $payment->update($paymentData);
            } else {
                $invoice->payments()->create($paymentData);
            }

            return $invoice->load(['contractor', 'items', 'payments']);
        });
    }
}

