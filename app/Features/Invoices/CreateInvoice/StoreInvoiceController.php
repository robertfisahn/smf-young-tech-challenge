<?php

declare(strict_types=1);

namespace App\Features\Invoices\CreateInvoice;

use Illuminate\Http\RedirectResponse;
use Throwable;

final class StoreInvoiceController
{
    public function __construct(
        private readonly CreateInvoiceAction $action
    ) {}

    public function __invoke(CreateInvoiceRequest $request): RedirectResponse
    {
        try {
            $invoice = $this->action->execute($request->validated());

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Faktura dodana pomyślnie.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Błąd podczas zapisu: ' . $e->getMessage());
        }
    }
}
