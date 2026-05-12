<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

use App\Common\Exceptions\OcrProcessingException;
use Illuminate\Support\Facades\Http;
use Throwable;

final readonly class GroqParserService
{
    private ?string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct(
        private AiPromptService $promptService
    ) {
        $this->apiKey = config('services.groq.api_key');
        $this->apiUrl = config('services.groq.api_url');
        $this->model = config('services.groq.model');
    }

    /**
     * @throws OcrProcessingException
     */
    public function parse(string $ocrText): array
    {
        if (empty($this->apiKey)) {
            throw new OcrProcessingException('GROQ_API_KEY is missing in configuration.');
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an accounting expert. You extract data from invoices and return ONLY clean JSON.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->promptService->getPrompt($ocrText),
                        ],
                    ],
                    'temperature' => 0.1,
                ]);

            if ($response->failed()) {
                throw new OcrProcessingException('Groq API Error: ' . $response->body());
            }

            $generatedText = $response->json('choices.0.message.content', '');

            return $this->extractJson($generatedText);
        } catch (Throwable $e) {
            if ($e instanceof OcrProcessingException) {
                throw $e;
            }
            throw new OcrProcessingException('Failed to connect to Groq AI: ' . $e->getMessage(), 0, $e);
        }
    }

    private function extractJson(string $text): array
    {
        preg_match('/\{.*\}/s', $text, $matches);
        $jsonCandidate = $matches[0] ?? $text;

        $data = json_decode($jsonCandidate, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new OcrProcessingException('AI returned invalid JSON format.');
        }

        return $data;
    }
}
