<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

use Illuminate\Support\Facades\App;

final readonly class AiParserFactory
{
    public function make(?string $provider = null): GroqParserService|OllamaParserService
    {
        $provider = $provider ?: 'groq';

        return match ($provider) {
            'ollama' => App::make(OllamaParserService::class),
            default => App::make(GroqParserService::class),
        };
    }
}
