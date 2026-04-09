<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OllamaParserService
{
    protected string $apiUrl;
    protected string $model;

    public function __construct(
        protected AiPromptService $promptService
    ) {
        $this->apiUrl = config('services.ollama.api_url');
        $this->model = config('services.ollama.model');
    }

    public function parse(string $ocrText): array
    {
        try {
            $response = Http::timeout(120)->post($this->apiUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $this->buildPrompt($ocrText),
                'stream' => false,
                'format' => 'json',
            ]);

            if ($response->failed()) {
                throw new \Exception('Ollama error: ' . $response->body());
            }

            $generatedText = $response->json('response', '');

            return $this->extractJson($generatedText);
        } catch (Throwable $e) {
            $message = $e->getMessage();
            $suggestion = 'Upewnij się, że aplikacja Ollama jest uruchomiona.';
            
            if (str_contains($message, 'model') && str_contains($message, 'not found')) {
                $suggestion = 'Wybrany model (' . $this->model . ') nie jest zainstalowany. Spróbuj pobrać go komendą "ollama pull ' . $this->model . '" lub użyj lżejszego modelu, np. "gemma3:1b", "llama3.2:1b" lub "gemma2:2b".';
            }

            return [
                'error' => 'Lokalny model AI (Ollama) napotkał problem: ' . $message,
                'suggestion' => $suggestion,
                'raw_text' => $ocrText,
            ];
        }
    }

    protected function buildPrompt(string $text): string
    {
        return $this->promptService->getPrompt($text);
    }

    protected function extractJson(string $text): array
    {
        preg_match('/\{.*\}/s', $text, $matches);
        $data = json_decode($matches[0] ?? $text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Ollama zwróciło niepoprawny JSON.', 'ai_response' => $text];
        }

        return $data;
    }
}
