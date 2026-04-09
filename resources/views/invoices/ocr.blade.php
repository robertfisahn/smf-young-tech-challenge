@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <style>
        input[type="radio"]:checked + span + svg { visibility: visible !important; }
        input[type="radio"]:checked ~ span span { color: #4f46e5; }
        input[type="radio"]:checked ~ span { border-color: #4f46e5; }
        label:has(input[type="radio"]:checked) { border-color: #4f46e5; ring: 2px; ring-color: #4f46e5; background-color: #f5f3ff; }
    </style>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Analizuj Fakturę (OCR)</h1>
            <p class="text-sm text-gray-600 mt-1">Prześlij plik PDF lub obraz faktury, aby automatycznie wyciągnąć dane.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('invoices.process-ocr') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Wybierz plik (PDF, JPG, PNG)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors cursor-pointer" onclick="document.getElementById('file-upload').click()">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <span class="relative rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">Prześlij plik</span>
                            <p class="pl-1">lub przeciągnij i upuść</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, PNG, JPG do 10MB</p>
                    </div>
                    <input id="file-upload" name="file" type="file" class="sr-only" required onchange="updateFileName(this)">
                </div>
                <p id="file-name" class="mt-2 text-sm text-gray-500 italic"></p>
            </div>

            <!-- AI Model Selector -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-semibold text-gray-700 mb-3">🛠️ Wybierz silnik AI:</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:border-indigo-400 transition-all">
                        <input type="radio" name="ai_provider" value="groq" class="sr-only" checked>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Groq (Chmura)</span>
                                <span class="mt-1 flex items-center text-xs text-gray-500 italic">Błyskawiczna analiza Llama 3</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-indigo-600 radio-check invisible" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:border-indigo-400 transition-all">
                        <input type="radio" name="ai_provider" value="ollama" class="sr-only">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Ollama (Lokalnie)</span>
                                <span class="mt-1 flex items-center text-xs text-gray-500 italic">Zero zależności od chmury</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-indigo-600 radio-check invisible" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
            </div>

            <!-- Opcje dodatkowe -->
            <div class="space-y-4">
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" name="auto_save" value="1" class="h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 transition-all">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors">🚀 Zapisz automatycznie po analizie (pomiń weryfikację)</span>
                </label>
            </div>

            <div class="pt-4">
                <button type="submit" id="submit-btn" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <span id="btn-text">Rozpocznij Analizę</span>
                    <span id="btn-loader" class="hidden ml-2">... 🔄</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const dropZone = document.querySelector('.border-dashed');
    const fileInput = document.getElementById('file-upload');
    const fileNameDisplay = document.getElementById('file-name');
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');

    // Drag and Drop handlers
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('border-indigo-500', 'bg-indigo-50'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-indigo-500', 'bg-indigo-50'), false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        updateFileName(fileInput);
    }, false);

    function updateFileName(input) {
        const fileName = input.files[0]?.name;
        if (fileName) {
            fileNameDisplay.textContent = 'Wybrany plik: ' + fileName;
            fileNameDisplay.classList.add('text-indigo-600', 'font-bold');
        }
    }

    form.onsubmit = function() {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        const selectedModel = document.querySelector('input[name="ai_provider"]:checked').value;
        btnText.textContent = 'Analizuję plik (' + selectedModel + ')...';
        btnLoader.classList.remove('hidden');
    };
</script>
@endsection
