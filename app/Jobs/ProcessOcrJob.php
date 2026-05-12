<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Infrastructure\OCR\OcrService;
use App\Infrastructure\AI\AiParserFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class ProcessOcrJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $filePath,
        public ?int $userId = null
    ) {}

    public function handle(OcrService $ocrService, AiParserFactory $aiParserFactory): void
    {
        try {
            $text = $ocrService->extractText($this->filePath);
            $aiData = $aiParserFactory->make()->parse($text);
            
            // Background processing logic (e.g. indexing) could go here
        } catch (Throwable) {
            // Processing failed quietly as requested in standards
        }
    }
}
