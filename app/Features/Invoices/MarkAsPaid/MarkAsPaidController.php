<?php

declare(strict_types=1);

namespace App\Features\Invoices\MarkAsPaid;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class MarkAsPaidController
{
    public function __construct(
        private readonly MarkAsPaidAction $action
    ) {}

    public function __invoke(Invoice $invoice): RedirectResponse
    {
        try {
            $this->action->execute($invoice);

            return back()->with('success', 'Faktura oznaczona jako opłacona.');
        } catch (Throwable $e) {
            return back()->with('error', 'Błąd: ' . $e->getMessage());
        }
    }
}
