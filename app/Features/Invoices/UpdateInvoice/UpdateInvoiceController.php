<?php

declare(strict_types=1);



namespace App\Features\Invoices\UpdateInvoice;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class UpdateInvoiceController
{
    public function __construct(
        private readonly UpdateInvoiceAction $action
    ) {}

    public function __invoke(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $this->action->execute($invoice, $request->validated());

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Faktura zaktualizowana pomyślnie.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Błąd podczas aktualizacji: ' . $e->getMessage());
        }
    }
}

