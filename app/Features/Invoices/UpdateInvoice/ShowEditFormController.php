<?php

declare(strict_types=1);



namespace App\Features\Invoices\UpdateInvoice;

use App\Models\Invoice;
use App\Features\Invoices\Shared\GetContractorsForSelectionAction;
use Illuminate\View\View;

final class ShowEditFormController
{
    public function __construct(
        private readonly GetContractorsForSelectionAction $getContractors
    ) {}

    public function __invoke(Invoice $invoice): View
    {
        // Załadowanie relacji i sumy dla formularza
        $invoice->load(['contractor', 'items', 'payments']);
        
        return view('invoices.edit', [
            'invoice' => $invoice,
            'contractors' => $this->getContractors->execute(),
        ]);
    }
}

