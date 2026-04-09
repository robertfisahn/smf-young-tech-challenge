@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <!-- Lewa strona: Nagłówek i data -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Faktura: {{ $invoice->invoice_number }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 font-medium">
                        Wystawiona dnia: {{ $invoice->date }}
                    </p>
                    <div class="mt-2 flex items-center space-x-2">
                        @php
                            $totalDue = $invoice->items->sum('total_price');
                            $totalPaid = $invoice->payments->whereNotNull('paid_at')->sum('amount');
                        @endphp
                        @if($totalDue > 0 && $totalPaid >= $totalDue)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-xs font-bold text-green-800 border border-green-200">Opłacona</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-xs font-bold text-yellow-800 border border-yellow-200">Oczekiwanie</span>
                        @endif
                    </div>
                </div>

                <!-- Prawa strona: Akcje -->
                <div class="flex flex-wrap items-center gap-2">
                    @if(!($totalDue > 0 && $totalPaid >= $totalDue))
                        <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_method" value="gotówka">
                            <input type="hidden" name="total_amount" value="{{ $totalDue }}">
                            <button type="submit" class="inline-flex items-center rounded-xl bg-green-50 px-3 py-2 text-xs font-bold text-green-700 border border-green-200 hover:bg-green-600 hover:text-white transition-all shadow-sm">
                                Oznacz jako opłacone
                            </button>
                        </form>
                    @endif

                    <div class="flex items-center bg-gray-50 rounded-xl p-1 border border-gray-200">
                        @if($invoice->file_path)
                            <a href="{{ route('invoices.file', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-bold text-indigo-600 hover:bg-white rounded-lg transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Podgląd
                            </a>
                        @endif
                        <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-white rounded-lg transition-all">✏️ Edytuj</a>
                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę fakturę?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-red-600 hover:bg-white rounded-lg transition-all">🗑️ Usuń</button>
                        </form>
                        <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-500 hover:bg-white rounded-lg transition-all">← Powrót</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-100">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Kontrahent</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-medium">{{ $invoice->contractor->name }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Adres</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->contractor->address }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wider">NIP</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->contractor->tax_id }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Metoda płatności</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->payments->first()->method ?? 'Nie określono' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50/50">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Kwota całkowita</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold text-lg">
                        {{ number_format($invoice->items->sum('total_price'), 2) }} {{ $invoice->payments->first()->currency ?? 'PLN' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Pozycje faktury</h3>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena jedn.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suma</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Brak pozycji dla tej faktury.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
