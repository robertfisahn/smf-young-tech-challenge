<?php

declare(strict_types=1);



namespace App\Features\Invoices\CreateInvoice;

use App\Features\Invoices\Shared\GetContractorsForSelectionAction;
use Illuminate\View\View;

final class ShowCreateFormController
{
    public function __construct(
        private readonly GetContractorsForSelectionAction $getContractors
    ) {}

    public function __invoke(): View
    {
        return view('invoices.create', [
            'contractors' => $this->getContractors->execute(),
        ]);
    }
}

