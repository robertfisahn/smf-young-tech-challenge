<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

use App\Common\Exceptions\OcrProcessingException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class OllamaParserService
{
    private string $apiUrl;
    private string $model;

    public function __construct(
        private AiPromptService $promptService
    ) {
        $this->apiUrl = config('services.ollama.api_url');
        $this->model = config('services.ollama.model');
    }

    /**
     * @throws OcrProcessingException
     */
    public function parse(string $ocrText): array
    {
        try {
            $response = Http::timeout(45)->post($this->apiUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $this->promptService->getPrompt($ocrText),
                'stream' => false,
                'format' => 'json',
            ]);

            if ($response->failed()) {
                throw new OcrProcessingException('Ollama Error: ' . $response->body());
            }

            $generatedText = $response->json('response', '');
            
            Log::debug("Ollama Raw Response: " . substr($generatedText, 0, 500));

            return $this->extractJson($generatedText);
        } catch (Throwable $e) {
            Log::error("Ollama Exception: " . $e->getMessage());
            if ($e instanceof OcrProcessingException) {
                throw $e;
            }

            $message = $e->getMessage();
            $suggestion = 'Make sure the Ollama application is running.';
            
            if (str_contains($message, 'model') && str_contains($message, 'not found')) {
                $suggestion = "The selected model ({$this->model}) is not installed. Run 'ollama pull {$this->model}'.";
            }

            throw new OcrProcessingException("Ollama processing failed: {$message}. Suggestion: {$suggestion}", 0, $e);
        }
    }

    private function extractJson(string $text): array
    {
        preg_match('/\{.*\}/s', $text, $matches);
        $data = json_decode($matches[0] ?? $text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new OcrProcessingException('Ollama returned invalid JSON format.');
        }

        return $data;
    }
}
