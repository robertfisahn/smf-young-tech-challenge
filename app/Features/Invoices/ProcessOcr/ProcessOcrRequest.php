<?php

declare(strict_types=1);



namespace App\Features\Invoices\ProcessOcr;

use Illuminate\Foundation\Http\FormRequest;

final class ProcessOcrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required_without:existing_file|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'existing_file' => 'nullable|string',
            'ai_provider' => 'nullable|string|in:groq,ollama',
            'auto_save' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Niedozwolony typ pliku. Dozwolone: pdf, jpg, jpeg, png.',
            'file.max' => 'Plik jest zbyt duży (max 10MB).',
            'ai_provider.in' => 'Nieprawidłowy dostawca AI.',
        ];
    }
}

