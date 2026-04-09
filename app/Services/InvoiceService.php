<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Contractor;
use Illuminate\Support\Collection;

class InvoiceService
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    public function getAll()
    {
        return Invoice::select(['id', 'contractor_id', 'invoice_number', 'date'])
            ->with([
                'contractor:id,name,tax_id',
                'items:id,invoice_id,name,quantity,unit_price,total_price',
                'payments:id,invoice_id,amount,currency,method,paid_at'
            ])
            ->latest()
            ->paginate(10);
    }

    public function getWithDetails(Invoice $invoice): Invoice
    {
        return $invoice->load(['contractor', 'items', 'payments']);
    }

    public function getAllContractors(): Collection
    {
        return Contractor::all();
    }

    public function create(array $data): Invoice
    {
        $invoiceData = collect($data)->except(['total_amount', 'currency'])->toArray();
        if (isset($invoiceData['invoice_number'])) {
            $invoiceData['invoice_number'] = $this->normalizeInvoiceNumber($invoiceData['invoice_number']);
        }
        $invoice = Invoice::create($invoiceData);
        
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        }

        if (isset($data['total_amount'])) {
            $method = strtolower($data['payment_method'] ?? 'przelew');
            $isImmediate = in_array($method, ['gotówka', 'karta']);

            $invoice->payments()->create([
                'amount' => $data['total_amount'],
                'currency' => $data['currency'] ?? 'PLN',
                'method' => $method,
                'paid_at' => $isImmediate ? $invoice->date : null,
            ]);
        }

        return $invoice;
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoiceData = collect($data)->except(['total_amount', 'currency'])->toArray();
        if (isset($invoiceData['invoice_number'])) {
            $invoiceData['invoice_number'] = $this->normalizeInvoiceNumber($invoiceData['invoice_number']);
        }
        $invoice->update($invoiceData);

        if (isset($data['total_amount'])) {
            $payment = $invoice->payments()->first();
            $method = strtolower($data['payment_method'] ?? 'przelew');
            $isImmediate = in_array($method, ['gotówka', 'karta']);

            if ($payment) {
                $payment->update([
                    'amount' => $data['total_amount'],
                    'currency' => $data['currency'] ?? 'PLN',
                    'method' => $method,
                    'paid_at' => $isImmediate ? $invoice->date : null,
                ]);
            } else {
                $invoice->payments()->create([
                    'amount' => $data['total_amount'],
                    'currency' => $data['currency'] ?? 'PLN',
                    'method' => $method,
                    'paid_at' => $isImmediate ? $invoice->date : null,
                ]);
            }
        }

        return $invoice;
    }

    public function delete(Invoice $invoice): void
    {
        $filePath = $invoice->file_path;

        $invoice->items()->delete();
        $invoice->payments()->delete();
        $invoice->delete();

        if ($filePath) {
            $this->fileUploadService->deleteFile($filePath);
        }
    }

    public function createFromAiData(array $aiData, ?string $filePath = null): Invoice
    {
        $nip = $aiData['contractor_nip'] ?? null;
        $name = $aiData['contractor_name'] ?? 'Nieznany Kontrahent';
        $address = $aiData['contractor_address'] ?? 'Adres nieustalony';

        $contractor = Contractor::where('tax_id', $nip)->first();

        if (!$contractor && $nip) {
            $contractor = Contractor::create([
                'name' => $name,
                'tax_id' => $nip,
                'address' => $address,
            ]);
        } elseif (!$contractor) {
                $contractor = Contractor::firstOrCreate(['name' => $name], ['address' => $address]);
        }

        $invoiceNumber = $this->normalizeInvoiceNumber($aiData['invoice_number'] ?? ('OCR-' . str_replace('.', '', microtime(true))));

        $invoice = Invoice::create([
            'contractor_id' => $contractor->id,
            'invoice_number' => $invoiceNumber,
            'date' => $aiData['date'] ?? now()->format('Y-m-d'),
            'file_path' => $filePath,
        ]);

        if (!isset($aiData['items']) || !is_array($aiData['items']) || count($aiData['items']) === 0) {
            throw new \Exception("Nie można zapisać faktury bez pozycji. OCR nie wykrył żadnych towarów ani usług.");
        }

        foreach ($aiData['items'] as $item) {
                $invoice->items()->create([
                    'name' => $item['name'] ?? ($item['description'] ?? 'Pozycja'),
                    'quantity' => $this->parseAmount($item['quantity'] ?? 1),
                    'unit_price' => $this->parseAmount($item['unit_price'] ?? ($item['price'] ?? 0)),
                    'total_price' => $this->parseAmount($item['total_price'] ?? ($item['total'] ?? 0)),
                ]);
            }
        
        $method = strtolower($aiData['payment_method'] ?? 'przelew');
        $isImmediate = in_array($method, ['gotówka', 'karta']);
        if (!in_array($method, ['przelew', 'gotówka', 'karta'])) {
            $method = 'przelew';
        }

        if (isset($aiData['total_amount'])) {
            $invoice->payments()->create([
                'amount' => $aiData['total_amount'],
                'currency' => $aiData['currency'] ?? 'PLN',
                'method' => $method,
                'paid_at' => $isImmediate ? ($aiData['payment_date'] ?? $invoice->date) : null,
            ]);
        }

        return $invoice;
    }

    private function parseAmount($val): float
    {
        if (is_numeric($val)) return (float)$val;
        
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $val));
        return (float)$clean;
    }

    private function normalizeInvoiceNumber(string $number): string
    {
        $prefixes = ['nr ', 'numer ', 'no. ', 'no ', '# ', 'nr.', 'nr:'];
        $clean = str_ireplace($prefixes, '', $number);
        return trim($clean);
    }
}
