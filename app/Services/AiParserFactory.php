<?php

namespace App\Services;

class AiParserFactory
{
    public function make(?string $provider = null): AiInvoiceParserService|OllamaParserService
    {
        $provider = $provider ?: 'groq';

        return match ($provider) {
            'ollama' => app(OllamaParserService::class),
            default => app(AiInvoiceParserService::class),
        };
    }
}
