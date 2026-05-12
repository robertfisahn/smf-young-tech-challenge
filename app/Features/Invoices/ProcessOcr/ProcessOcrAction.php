<?php

declare(strict_types=1);

namespace App\Features\Invoices\ProcessOcr;

use App\Infrastructure\AI\AiParserFactory;
use App\Infrastructure\OCR\OcrService;
use App\Infrastructure\Files\FileUploadService;
use App\Features\Invoices\Shared\CreateInvoiceFromAiDataAction;
use App\Common\Exceptions\OcrProcessingException;
use App\Jobs\ProcessOcrJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class ProcessOcrAction
{
    public function __construct(
        private OcrService $ocrService,
        private AiParserFactory $aiParserFactory,
        private FileUploadService $fileUploadService,
        private CreateInvoiceFromAiDataAction $createInvoiceAction
    ) {}

    public function execute(
        ?UploadedFile $file,
        ?string $existingFile,
        ?string $aiProvider,
        bool $autoSave,
        ?int $userId = null
    ): array {
        set_time_limit(90);
        $start = microtime(true);

        try {
            // 1. Handle File
            $fullPath = $file 
                ? $this->fileUploadService->storeInvoiceFile($file)
                : storage_path('app/' . $existingFile);

            if (!file_exists($fullPath)) {
                throw new OcrProcessingException('File not found: ' . $fullPath);
            }

            // 2. OCR Extraction
            $ocrStart = microtime(true);
            $text = $this->ocrService->extractText($fullPath);
            $ocrDuration = microtime(true) - $ocrStart;
            Log::info("OCR Duration: {$ocrDuration}s");

            // 3. AI Parsing
            $aiStart = microtime(true);
            
            // Optymalizacja: Czyścimy tekst i ograniczamy go, żeby nie zapchać Ollamy
            $cleanText = $this->sanitizeOcrText($text);
            
            $aiData = $this->aiParserFactory->make($aiProvider)->parse($cleanText);
            $aiDuration = microtime(true) - $aiStart;
            Log::info("AI Parsing Duration: {$aiDuration}s");

            // 4. Auto-save if requested
            $invoiceId = null;
            if ($autoSave && !empty($aiData)) {
                $invoice = $this->createInvoiceAction->execute($aiData);
                $invoiceId = $invoice->id;
                
                // Move file to permanent storage
                if ($file) {
                    $this->fileUploadService->moveToPermanent($fullPath);
                }
            }

            Log::info("Total ProcessOcr Duration: " . (microtime(true) - $start) . "s");

            return [
                'text' => $text,
                'ai_data' => $aiData,
                'file_path' => $file ? 'tmp/invoices/' . basename($fullPath) : $existingFile,
                'invoice_id' => $invoiceId
            ];

        } catch (Throwable $e) {
            Log::error("OCR/AI Processing failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function sanitizeOcrText(string $text): string
    {
        // Usuwamy nadmiarowe spacje, entery i ograniczamy do 10k znaków
        $text = preg_replace('/\s+/', ' ', $text);
        return mb_substr(trim($text), 0, 10000);
    }
}
