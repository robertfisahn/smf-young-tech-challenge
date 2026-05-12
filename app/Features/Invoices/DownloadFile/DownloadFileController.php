<?php

declare(strict_types=1);



namespace App\Features\Invoices\DownloadFile;

use App\Models\Invoice;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class DownloadFileController
{
    public function __invoke(Invoice $invoice): BinaryFileResponse
    {
        if (!$invoice->file_path) {
            abort(404, 'Do tej faktury nie przypisano żadnego pliku.');
        }

        $path = storage_path('app/' . $invoice->file_path);

        if (!file_exists($path)) {
            abort(404, 'Plik nie istnieje na dysku.');
        }

        return response()->file($path);
    }
}

