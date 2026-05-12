<?php

declare(strict_types=1);



namespace App\Features\Invoices\GenerateSample;

use Illuminate\View\View;

final class ShowGenerateFormController
{
    public function __invoke(): View
    {
        return view('invoices.generate');
    }
}

