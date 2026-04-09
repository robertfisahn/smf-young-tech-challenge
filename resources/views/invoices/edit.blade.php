@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6 text-gray-900">Edytuj fakturę</h1>
    
    <form action="{{ route('invoices.update', $invoice) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="contractor_id" class="block text-sm font-medium text-gray-700">Kontrahent</label>
                <select name="contractor_id" id="contractor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($contractors as $contractor)
                        <option value="{{ $contractor->id }}" {{ $invoice->contractor_id == $contractor->id ? 'selected' : '' }}>{{ $contractor->name }} ({{ $contractor->tax_id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="invoice_number" class="block text-sm font-medium text-gray-700">Numer Faktury</label>
                <input type="text" name="invoice_number" id="invoice_number" value="{{ $invoice->invoice_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Data wystawienia</label>
                    <input type="date" name="date" id="date" value="{{ $invoice->date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Waluta</label>
                    <input type="text" name="currency" id="currency" value="{{ $invoice->currency }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    @php $method = $invoice->payments->first()->method ?? 'przelew'; @endphp
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="przelew" {{ $method == 'przelew' ? 'selected' : '' }}>Przelew</option>
                        <option value="gotówka" {{ $method == 'gotówka' ? 'selected' : '' }}>Gotówka</option>
                        <option value="karta" {{ $method == 'karta' ? 'selected' : '' }}>Karta</option>
                    </select>
                </div>
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700">Kwota Brutto</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" value="{{ $invoice->total_amount }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end">
                    <a href="{{ route('invoices.show', $invoice) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Anuluj</a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Zapisz zmiany</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
