<?php

namespace App\Jobs;

use App\Services\OcrService;
use App\Services\AiParserFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessOcrJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $filePath,
        public ?string $userId = null
    ) {}

    public function handle(OcrService $ocrService, AiParserFactory $aiParserFactory): void
    {
        try {
            $text = $ocrService->extractText($this->filePath);
            $aiData = $aiParserFactory->make()->parse($text);
        } catch (\Exception $e) {
            // Processing failed quietly as requested
        }
    }
}
