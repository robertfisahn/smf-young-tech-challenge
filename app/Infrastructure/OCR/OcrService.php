<?php

declare(strict_types=1);

namespace App\Infrastructure\OCR;

use App\Common\Exceptions\OcrProcessingException;
use Smalot\PdfParser\Parser;
use Throwable;

final readonly class OcrService
{
    private Parser $pdfParser;
    private string $tesseractPath;

    public function __construct()
    {
        $this->pdfParser = new Parser();
        $this->tesseractPath = config('services.tesseract.path', 'tesseract');
    }

    /**
     * @throws OcrProcessingException
     */
    public function extractText(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return $this->extractFromPdf($filePath);
        }

        return $this->extractFromImage($filePath);
    }

    private function extractFromPdf(string $filePath): string
    {
        try {
            // Próbujemy wyciągnąć warstwę tekstową (najszybsza metoda)
            $pdf = $this->pdfParser->parseFile($filePath);
            $text = $pdf->getText();

            if (empty(trim($text))) {
                return $this->extractFromImage($filePath);
            }

            return $text;
        } catch (Throwable) {
            // Jeśli parser PDF zawiedzie, robimy fallback do OCR obrazkowego
            return $this->extractFromImage($filePath);
        }
    }

    private function extractFromImage(string $filePath): string
    {
        $tempId = time() . '_' . uniqid();
        $outputPath = storage_path('app/temp_ocr_' . $tempId);
        $resultFile = $outputPath . '.txt';

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $tessdataDir = str_replace('tesseract.exe', 'tessdata', $this->tesseractPath);
                // Dodajemy timeout na poziomie systemu operacyjnego (opcjonalnie, ale tu polegamy na PHP)
                $command = "set \"TESSDATA_PREFIX={$tessdataDir}\"&&\"{$this->tesseractPath}\" \"{$filePath}\" \"{$outputPath}\" -l pol+eng --psm 6 2>&1";
            } else {
                $command = "tesseract \"{$filePath}\" \"{$outputPath}\" -l pol+eng --psm 6 2>&1";
            }

            $output = shell_exec($command);

            if (file_exists($resultFile)) {
                $text = (string) file_get_contents($resultFile);
                return trim($text);
            }

            throw new OcrProcessingException("OCR Failed: Could not extract text. Tesseract output: {$output}");
        } finally {
            // ZAWSZE usuwamy plik wynikowy, aby nie śmiecić
            if (file_exists($resultFile)) {
                @unlink($resultFile);
            }
        }
    }
}
