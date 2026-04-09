@extends('layouts.app')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">Lista Faktur</h1>
        <p class="mt-2 text-sm text-gray-700">Zarządzaj swoimi fakturami i przesyłaj nowe pliki do analizy OCR.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex space-x-3">
        <a href="{{ route('invoices.ocr') }}" class="block rounded-md bg-white px-3 py-2 text-center text-sm font-semibold text-indigo-600 shadow-sm ring-1 ring-inset ring-indigo-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Analizuj OCR</a>
        <a href="{{ route('invoices.create') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Dodaj Fakturę</a>
    </div>
</div>

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Numer Faktury</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kontrahent</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Data</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kwota</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Akcje</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($invoices as $invoice)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $invoice->invoice_number }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $invoice->contractor->name }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $invoice->date }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ number_format($invoice->items->sum('total_price'), 2) }} {{ $invoice->payments->first()->currency ?? 'PLN' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                @php
                                    $totalDue = $invoice->items->sum('total_price');
                                    $totalPaid = $invoice->payments->whereNotNull('paid_at')->sum('amount');
                                @endphp
                                @if($totalDue > 0 && $totalPaid >= $totalDue)
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">Opłacona</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-bold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Oczekiwanie</span>
                                @endif
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div class="flex items-center justify-end space-x-3">
                                    @if(!($totalDue > 0 && $totalPaid >= $totalDue))
                                        <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="payment_method" value="gotówka">
                                            <input type="hidden" name="total_amount" value="{{ $totalDue }}">
                                            <button type="submit" class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-[10px] font-bold text-green-700 border border-green-200 hover:bg-green-600 hover:text-white transition-colors duration-200">
                                                Oznacz jako opłacone
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">Pokaż</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">Brak faktur w bazie.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
