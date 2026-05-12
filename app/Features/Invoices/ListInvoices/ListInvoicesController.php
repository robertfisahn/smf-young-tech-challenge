<?php

declare(strict_types=1);



namespace App\Features\Invoices\ListInvoices;

use Illuminate\View\View;

final class ListInvoicesController
{
    public function __construct(
        private readonly ListInvoicesAction $action
    ) {}

    public function __invoke(): View
    {
        return view('invoices.index', [
            'invoices' => $this->action->execute(),
        ]);
    }
}

