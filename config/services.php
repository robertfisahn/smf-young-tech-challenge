<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'tesseract' => [
        'path' => env('TESSERACT_PATH', 'tesseract'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'api_url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
    ],

    'ollama' => [
        'api_url' => file_exists('/.dockerenv') 
            ? 'http://host.docker.internal:11434' 
            : env('OLLAMA_URL', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.1'),
    ],

];
