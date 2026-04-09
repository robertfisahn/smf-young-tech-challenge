<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessOcrRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Niedozwolony typ pliku. Dozwolone: pdf, jpg, jpeg, png',
            'file.max' => 'Plik jest zbyt duży. Maksymalny rozmiar: 10MB',
        ];
    }
}
