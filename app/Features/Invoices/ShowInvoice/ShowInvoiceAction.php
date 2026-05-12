<?php

declare(strict_types=1);



namespace App\Features\Invoices\ShowInvoice;

use App\Models\Invoice;
use App\Common\Exceptions\NotFoundException;
use Throwable;

final readonly class ShowInvoiceAction
{
    /**
     * @throws NotFoundException
     */
    public function execute(int $id): Invoice
    {
        try {
            return Invoice::query()
                ->with(['contractor', 'items', 'payments'])
                ->withSum('payments as total_amount', 'amount')
                ->findOrFail($id);
        } catch (Throwable) {
            throw new NotFoundException('Invoice', $id);
        }
    }
}

