<?php

declare(strict_types=1);



namespace App\Features\Invoices\ShowInvoice;

use Illuminate\View\View;
use Throwable;

final class ShowInvoiceController
{
    public function __construct(
        private readonly ShowInvoiceAction $action
    ) {}

    public function __invoke(int $id): View
    {
        try {
            $invoice = $this->action->execute($id);

            return view('invoices.show', [
                'invoice' => $invoice,
            ]);
        } catch (Throwable $e) {
            abort(404, $e->getMessage());
        }
    }
}

