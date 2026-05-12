<?php

declare(strict_types=1);

namespace App\Features\Invoices\MarkAsPaid;

use App\Models\Invoice;
use Carbon\Carbon;

final readonly class MarkAsPaidAction
{
    public function execute(Invoice $invoice): void
    {
        $payment = $invoice->payments()->first();
        
        if ($payment) {
            $payment->update([
                'paid_at' => Carbon::now(),
            ]);
        } else {
            // Jeśli faktura nie ma płatności (mało prawdopodobne), tworzymy ją
            $invoice->payments()->create([
                'amount' => $invoice->items->sum('total_price'),
                'currency' => 'PLN',
                'method' => 'przelew',
                'paid_at' => Carbon::now(),
            ]);
        }
    }
}
