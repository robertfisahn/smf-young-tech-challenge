<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public function storeInvoiceFile(UploadedFile $file): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $tempDir = storage_path('app/tmp/invoices');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $file->move($tempDir, $fileName);

        return $tempDir . DIRECTORY_SEPARATOR . $fileName;
    }

    public function moveToPermanent(string $tempPath): string
    {
        if (!file_exists($tempPath)) {
            return $tempPath;
        }

        $fileName = basename($tempPath);
        $targetDir = storage_path('app/invoices');
        $relativePath = 'invoices/' . $fileName;

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        rename($tempPath, $targetDir . DIRECTORY_SEPARATOR . $fileName);

        return $relativePath;
    }

    public function deleteFile(string $relativePath): void
    {
        $path = storage_path('app/' . $relativePath);

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
