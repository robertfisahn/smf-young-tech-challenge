<?php

declare(strict_types=1);



namespace App\Features\Invoices\ProcessOcr;

use Illuminate\View\View;

final class ShowOcrFormController
{
    public function __invoke(): View
    {
        return view('invoices.ocr');
    }
}

