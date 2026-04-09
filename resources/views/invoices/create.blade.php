@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6 text-gray-900">Dodaj nową fakturę</h1>
    
    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="contractor_id" class="block text-sm font-medium text-gray-700">Kontrahent</label>
                <select name="contractor_id" id="contractor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($contractors as $contractor)
                        <option value="{{ $contractor->id }}">{{ $contractor->name }} ({{ $contractor->tax_id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="invoice_number" class="block text-sm font-medium text-gray-700">Numer Faktury</label>
                <input type="text" name="invoice_number" id="invoice_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Data wystawienia</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Waluta</label>
                    <input type="text" name="currency" id="currency" value="PLN" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="przelew" selected>Przelew</option>
                        <option value="gotówka">Gotówka</option>
                        <option value="karta">Karta</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="total_amount" class="block text-sm font-medium text-gray-700">Kwota Brutto (Suma)</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50" readonly>
                <p class="mt-1 text-xs text-gray-500 italic">Suma obliczana automatycznie na podstawie pozycji poniżej.</p>
            </div>

            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pozycje faktury</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="items-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nazwa</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Ilość</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Cena jedn.</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Suma</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="items-body">
                            <!-- Rows will be added here -->
                        </tbody>
                    </table>
                </div>
                <button type="button" onclick="addItemRow()" class="mt-4 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    + Dodaj pozycję
                </button>
            </div>

            <script>
                let itemCount = 0;

                function addItemRow() {
                    const body = document.getElementById('items-body');
                    const row = document.createElement('tr');
                    row.id = `item-row-${itemCount}`;
                    row.innerHTML = `
                        <td class="px-3 py-4 text-sm">
                            <input type="text" name="items[${itemCount}][name]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </td>
                        <td class="px-3 py-4 text-sm">
                            <input type="number" name="items[${itemCount}][quantity]" step="1" min="1" value="1" oninput="calculateRow(${itemCount})" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </td>
                        <td class="px-3 py-4 text-sm">
                            <input type="number" name="items[${itemCount}][unit_price]" step="0.01" min="0" value="0.00" oninput="calculateRow(${itemCount})" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </td>
                        <td class="px-3 py-4 text-sm font-medium text-gray-900">
                            <span id="item-total-${itemCount}">0.00</span>
                        </td>
                        <td class="px-3 py-4 text-sm text-right">
                            <button type="button" onclick="removeItemRow(${itemCount})" class="text-red-600 hover:text-red-900">✕</button>
                        </td>
                    `;
                    body.appendChild(row);
                    itemCount++;
                }

                function removeItemRow(id) {
                    const row = document.getElementById(`item-row-${id}`);
                    row.remove();
                    calculateTotal();
                }

                function calculateRow(id) {
                    const qty = document.getElementsByName(`items[${id}][quantity]`)[0].value;
                    const price = document.getElementsByName(`items[${id}][unit_price]`)[0].value;
                    const total = (parseFloat(qty) * parseFloat(price)).toFixed(2);
                    document.getElementById(`item-total-${id}`).innerText = total;
                    calculateTotal();
                }

                function calculateTotal() {
                    let total = 0;
                    const rows = document.querySelectorAll('#items-body tr');
                    rows.forEach(row => {
                        const id = row.id.split('-').pop();
                        const rowTotal = document.getElementById(`item-total-${id}`).innerText;
                        total += parseFloat(rowTotal);
                    });
                    document.getElementById('total_amount').value = total.toFixed(2);
                }

                // Add initial row
                window.onload = addItemRow;
            </script>

            <div class="pt-5">
                <div class="flex justify-end">
                    <a href="{{ route('invoices.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">Anuluj</a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">Zapisz</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
