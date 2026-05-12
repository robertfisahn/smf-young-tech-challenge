<?php

declare(strict_types=1);



namespace App\Features\Invoices\ProcessOcr;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

final class ProcessOcrController
{
    public function __construct(
        private readonly ProcessOcrAction $action
    ) {}

    public function __invoke(ProcessOcrRequest $request): View|RedirectResponse
    {
        try {
            $result = $this->action->execute(
                $request->file('file'),
                $request->input('existing_file'),
                $request->input('ai_provider'),
                $request->boolean('auto_save'),
                (int) auth()->id()
            );

            if ($result['invoice_id']) {
                return redirect()
                    ->route('invoices.show', $result['invoice_id'])
                    ->with('success', 'Faktura przetworzona i zapisana automatycznie.');
            }

            return view('invoices.ocr_result', [
                'text' => $result['text'],
                'ai_data' => $result['ai_data'],
                'file_path' => $result['file_path'],
                'ai_provider' => $request->input('ai_provider'),
            ]);

        } catch (Throwable $e) {
            return redirect()
                ->route('invoices.ocr')
                ->with('error', 'Błąd przetwarzania: ' . $e->getMessage());
        }
    }
}

