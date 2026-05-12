<?php

declare(strict_types=1);



namespace App\Features\Invoices\Shared;

use App\Common\Exceptions\DomainException;
use App\Models\Contractor;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateInvoiceFromAiDataAction
{
    /**
     * @throws DomainException
     */
    public function execute(array $aiData, ?string $filePath = null): Invoice
    {
        return DB::transaction(function () use ($aiData, $filePath) {
            try {
                $contractor = $this->getOrCreateContractor($aiData);
                $invoice = $this->createInvoice($contractor, $aiData, $filePath);
                $this->createItems($invoice, $aiData);
                $this->createPayment($invoice, $aiData);

                return $invoice;
            } catch (Throwable $e) {
                if ($e instanceof DomainException) {
                    throw $e;
                }
                throw new DomainException('Błąd podczas zapisu faktury z danych AI: ' . $e->getMessage(), 0, $e);
            }
        });
    }

    private function getOrCreateContractor(array $aiData): Contractor
    {
        $nip = $aiData['contractor_nip'] ?? null;
        $name = $aiData['contractor_name'] ?? 'Nieznany Kontrahent';
        $address = $aiData['contractor_address'] ?? 'Adres nieustalony';

        if ($nip) {
            return Contractor::firstOrCreate(
                ['tax_id' => $nip],
                ['name' => $name, 'address' => $address]
            );
        }

        return Contractor::firstOrCreate(
            ['name' => $name],
            ['address' => $address]
        );
    }

    private function createInvoice(Contractor $contractor, array $aiData, ?string $filePath): Invoice
    {
        $invoiceNumber = $this->normalizeInvoiceNumber($aiData['invoice_number'] ?? ('OCR-' . time()));

        // Zapobieganie duplikatom (unikalny numer faktury)
        if (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
            throw new DomainException("Faktura o numerze {$invoiceNumber} już istnieje w bazie danych. Nie można dodać jej ponownie.");
        }

        return Invoice::create([
            'contractor_id' => $contractor->id,
            'invoice_number' => $invoiceNumber,
            'date' => $aiData['date'] ?? now()->format('Y-m-d'),
            'file_path' => $filePath,
        ]);
    }

    private function createItems(Invoice $invoice, array $aiData): void
    {
        $items = $aiData['items'] ?? [];

        if (empty($items)) {
            throw new DomainException("Nie można zapisać faktury bez pozycji. OCR nie wykrył żadnych towarów ani usług.");
        }

        foreach ($items as $item) {
            $invoice->items()->create([
                'name' => $item['name'] ?? ($item['description'] ?? 'Pozycja'),
                'quantity' => $this->parseAmount($item['quantity'] ?? 1),
                'unit_price' => $this->parseAmount($item['unit_price'] ?? ($item['price'] ?? 0)),
                'total_price' => $this->parseAmount($item['total_price'] ?? ($item['total'] ?? 0)),
            ]);
        }
    }

    private function createPayment(Invoice $invoice, array $aiData): void
    {
        if (!isset($aiData['total_amount'])) {
            return;
        }

        $method = strtolower($aiData['payment_method'] ?? 'przelew');
        $isImmediate = in_array($method, ['gotówka', 'karta']);
        
        if (!in_array($method, ['przelew', 'gotówka', 'karta'])) {
            $method = 'przelew';
        }

        $invoice->payments()->create([
            'amount' => $aiData['total_amount'],
            'currency' => $aiData['currency'] ?? 'PLN',
            'method' => $method,
            'paid_at' => $isImmediate ? ($aiData['payment_date'] ?? $invoice->date) : null,
        ]);
    }

    private function parseAmount(mixed $val): float
    {
        if (is_numeric($val)) return (float)$val;
        
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '.', (string)$val));
        return (float)$clean;
    }

    private function normalizeInvoiceNumber(string $number): string
    {
        $prefixes = ['nr ', 'numer ', 'no. ', 'no ', '# ', 'nr.', 'nr:'];
        $clean = str_ireplace($prefixes, '', $number);
        return trim($clean);
    }
}

