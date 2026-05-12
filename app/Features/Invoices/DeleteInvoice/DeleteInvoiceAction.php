<?php

declare(strict_types=1);



namespace App\Features\Invoices\DeleteInvoice;

use App\Models\Invoice;

final readonly class DeleteInvoiceAction
{
    public function execute(Invoice $invoice): void
    {
        // Używamy Soft Delete - rekord zostaje w bazie z flagą deleted_at
        // Relacje (items, payments) zostają, aby zachować integralność danych historycznych
        $invoice->delete();
    }
}

