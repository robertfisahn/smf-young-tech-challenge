<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Throwable;

class OcrService
{
    protected Parser $pdfParser;
    protected string $tesseractPath;

    public function __construct()
    {
        $this->pdfParser = new Parser();
        $this->tesseractPath = config('services.tesseract.path', 'tesseract');
    }

    public function extractText(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return $this->extractFromPdf($filePath);
        }

        return $this->extractFromImage($filePath);
    }

    protected function extractFromPdf(string $filePath): string
    {
        try {
            $pdf = $this->pdfParser->parseFile($filePath);
            $text = $pdf->getText();

            if (empty(trim($text))) {
                return $this->extractFromImage($filePath);
            }

            return $text;
        } catch (Throwable) {
            return $this->extractFromImage($filePath);
        }
    }

    protected function extractFromImage(string $filePath): string
    {
        $outputPath = storage_path('app/temp_ocr_' . time());

        if (PHP_OS_FAMILY === 'Windows') {
            $tessdataDir = str_replace('tesseract.exe', 'tessdata', $this->tesseractPath);
            $command = "set \"TESSDATA_PREFIX={$tessdataDir}\"&&\"{$this->tesseractPath}\" \"{$filePath}\" \"{$outputPath}\" -l pol+eng --psm 6 2>&1";
        } else {
            // Linux / Docker
            $command = "tesseract \"{$filePath}\" \"{$outputPath}\" -l pol+eng --psm 6 2>&1";
        }

        $output = shell_exec($command);

        $resultFile = $outputPath . '.txt';

        if (file_exists($resultFile)) {
            $text = file_get_contents($resultFile);
            unlink($resultFile);

            return trim($text);
        }

        return "Błąd OCR: Nie udało się wyciągnąć tekstu. Sprawdź czy plik jest czytelny i czy Tesseract jest zainstalowany.";
    }
}
