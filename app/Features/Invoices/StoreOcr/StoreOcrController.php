<?php

declare(strict_types=1);



namespace App\Features\Invoices\StoreOcr;

use App\Features\Invoices\Shared\CreateInvoiceFromAiDataAction;
use App\Infrastructure\Files\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class StoreOcrController
{
    public function __construct(
        private readonly CreateInvoiceFromAiDataAction $createInvoiceAction,
        private readonly FileUploadService $fileUploadService
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $aiData = json_decode((string) $request->input('ai_data'), true);
        $filePath = (string) $request->input('file_path');

        if (!$aiData) {
            return back()->with('error', 'Nieprawidłowe dane AI.');
        }

        try {
            $permanentPath = $this->fileUploadService->moveToPermanent($filePath);
            $invoice = $this->createInvoiceAction->execute($aiData, $permanentPath);

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Faktura została zapisana pomyślnie na podstawie analizy AI.');

        } catch (Throwable $e) {
            return redirect()
                ->route('invoices.ocr')
                ->with('error', 'Błąd podczas zapisu: ' . $e->getMessage());
        }
    }
}

