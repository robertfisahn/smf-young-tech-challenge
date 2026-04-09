<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiInvoiceParserService
{
    protected ?string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct(
        protected AiPromptService $promptService
    ) {
        $this->apiKey = config('services.groq.api_key');
        $this->apiUrl = config('services.groq.api_url');
        $this->model = config('services.groq.model');
    }

    public function parse(string $ocrText): array
    {
        if (str_starts_with($ocrText, 'Błąd OCR:')) {
            return [
                'error' => $ocrText,
                'raw_text' => $ocrText,
            ];
        }

        if (empty($this->apiKey)) {
            return [
                'error' => 'Brak klucza GROQ_API_KEY w pliku .env.',
                'raw_text' => $ocrText,
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Jesteś ekspertem od księgowości. Wyciągasz dane z faktur i zwracasz WYŁĄCZNIE czysty JSON.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->buildPrompt($ocrText),
                        ],
                    ],
                    'temperature' => 0.1,
                ]);

            if ($response->failed()) {
                throw new \Exception('Błąd API Groq: ' . $response->body());
            }

            $generatedText = $response->json('choices.0.message.content', '');

            return $this->extractJson($generatedText);
        } catch (Throwable $e) {
            return [
                'error' => 'Błąd połączenia z Groq AI (Chmura): ' . $e->getMessage(),
                'suggestion' => 'Sprawdź swoje połączenie internetowe lub spróbuj użyć lokalnego modelu (Ollama), jeśli go posiadasz.',
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
        $jsonCandidate = $matches[0] ?? $text;

        $data = json_decode($jsonCandidate, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'AI zwróciło niepoprawny format danych.',
                'ai_response' => $text,
            ];
        }

        return $data;
    }
}
