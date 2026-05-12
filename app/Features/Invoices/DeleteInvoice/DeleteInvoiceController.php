<?php

declare(strict_types=1);



namespace App\Features\Invoices\DeleteInvoice;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class DeleteInvoiceController
{
    public function __construct(
        private readonly DeleteInvoiceAction $action
    ) {}

    public function __invoke(Invoice $invoice): RedirectResponse
    {
        try {
            $this->action->execute($invoice);

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Faktura została przeniesiona do kosza.');
        } catch (Throwable $e) {
            return back()->with('error', 'Błąd podczas usuwania: ' . $e->getMessage());
        }
    }
}

