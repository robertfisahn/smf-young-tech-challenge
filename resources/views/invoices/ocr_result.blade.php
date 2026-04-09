@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Wynik Analizy OCR</h1>
                <p class="text-sm text-gray-600 mt-1">Oto surowy tekst wyciągnięty z Twojego pliku.</p>
            </div>
            <a href="{{ route('invoices.ocr') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Prześlij kolejny →</a>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Wyciągnięty tekst:</h2>
            <pre class="whitespace-pre-wrap text-sm text-gray-800 font-mono leading-relaxed bg-white p-4 rounded border border-gray-300 max-h-96 overflow-y-auto">
{{ $text }}
            </pre>
        </div>

        <div class="mt-10 border-t border-gray-200 pt-10">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="bg-indigo-100 text-indigo-700 p-2 rounded-lg mr-3">🤖</span>
                Analiza Agenta AI (Ustrukturyzowane dane)
            </h2>

            @if(isset($ai_data['error']))
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-900 font-bold">
                                {{ $ai_data['error'] }}
                            </p>
                            @if(isset($ai_data['suggestion']))
                                <p class="mt-1 text-sm text-amber-700">
                                    💡 <strong>Sugestia:</strong> {{ $ai_data['suggestion'] }}
                                </p>
                            @endif

                            <?php 
                                $currentProvider = $ai_provider ?? config('services.ai.provider');
                                $otherProvider = ($currentProvider === 'ollama') ? 'groq' : 'ollama';
                                $otherProviderLabel = ($otherProvider === 'groq') ? 'Groq (Chmura)' : 'Ollama (Lokalnie)';
                            ?>

                            <div class="mt-4">
                                <form action="{{ route('invoices.process-ocr') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="existing_file" value="{{ $file_path }}">
                                    <input type="hidden" name="ai_provider" value="{{ $otherProvider }}">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-amber-300 shadow-sm text-sm font-medium rounded-md text-amber-800 bg-amber-100 hover:bg-amber-200 transition-colors">
                                        🔄 Spróbuj ponownie używając {{ $otherProviderLabel }}
                                    </button>
                                </form>
                            </div>

                            @if(isset($ai_data['ai_response']))
                                <div class="mt-4 text-xs font-mono text-amber-800 bg-amber-100 p-2 rounded max-h-48 overflow-y-auto">
                                    {{ $ai_data['ai_response'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-8 mb-10">
                    <!-- Kontrahent -->
                    <div class="border border-gray-300 rounded overflow-hidden">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-300 text-xs font-bold uppercase">Dane Kontrahenta</div>
                        <table class="min-w-full text-sm">
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2 text-gray-500 w-1/3">Nazwa firmy:</td>
                                <td class="px-4 py-2 font-bold">{{ $ai_data['contractor_name'] ?? '---' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2 text-gray-500">Adres:</td>
                                <td class="px-4 py-2">{{ $ai_data['contractor_address'] ?? '---' }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-500">NIP:</td>
                                <td class="px-4 py-2 font-mono">{{ $ai_data['contractor_nip'] ?? '---' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Dane Faktury i Płatności -->
                    <div class="border border-gray-300 rounded overflow-hidden">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-300 text-xs font-bold uppercase">Dane Dokumentu i Płatności</div>
                        <table class="min-w-full text-sm">
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2 text-gray-500 w-1/3">Numer faktury:</td>
                                <td class="px-4 py-2 font-bold">{{ $ai_data['invoice_number'] ?? '---' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2 text-gray-500">Data wystawienia:</td>
                                <td class="px-4 py-2">{{ $ai_data['date'] ?? '---' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2 text-gray-500">Metoda płatności:</td>
                                <td class="px-4 py-2">{{ $ai_data['payment_method'] ?? '---' }}</td>
                            </tr>
                            @if(isset($ai_data['payment_method']) && !str_contains(strtolower($ai_data['payment_method']), 'gotów'))
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2 text-gray-500">Termin / Data płatności:</td>
                                <td class="px-4 py-2">{{ $ai_data['payment_date'] ?? '---' }}</td>
                            </tr>
                            @endif
                            <tr class="bg-gray-50">
                                <td class="px-4 py-3 text-gray-700 uppercase text-xs">Do zapłaty:</td>
                                <td class="px-4 py-3 text-base">{{ number_format((float)($ai_data['total_amount'] ?? 0), 2) }} {{ $ai_data['currency'] ?? 'PLN' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-100 px-4 py-2 border border-gray-300 border-b-0 rounded-t text-xs font-bold uppercase">Pozycje faktury</div>
                @if(isset($ai_data['items']) && is_array($ai_data['items']))
                <div class="overflow-x-auto border border-gray-300 rounded-b mb-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-bold uppercase text-gray-500">
                                <th class="px-4 py-2 text-left">Pozycja</th>
                                <th class="px-4 py-2 text-right">Ilość</th>
                                <th class="px-4 py-2 text-right">Cena</th>
                                <th class="px-4 py-2 text-right">Wartość</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ai_data['items'] as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item['name'] ?? '---' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ $item['quantity'] ?? '1' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format((float)($item['unit_price'] ?? 0), 2) }}</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format((float)($item['total_price'] ?? 0), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endif

            <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-r">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-indigo-700 italic">
                            Agent AI automatycznie oczyścił dane z błędów OCR i przygotował je do zapisu w bazie danych.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('invoices.index') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Wróć do listy</a>
            @if(!isset($ai_data['error']))
                <form action="{{ route('invoices.store_ocr') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ai_data" value="{{ json_encode($ai_data) }}">
                    <input type="hidden" name="file_path" value="{{ $file_path }}">
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        ✅ Zapisz fakturę w bazie
                    </button>
                </form>
            @endif
        </div>

    </div>
</div>
@endsection
