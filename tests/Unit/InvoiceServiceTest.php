<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Invoice;
use App\Models\Contractor;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoiceService::class);
    }

    public function test_can_create_invoice(): void
    {
        $contractor = Contractor::create([
            'name' => 'Test Contractor',
            'tax_id' => '1234567890',
            'address' => 'Test Address'
        ]);

        $data = [
            'contractor_id' => $contractor->id,
            'invoice_number' => 'INV/TEST/001',
            'date' => '2026-04-08',
            'total_amount' => 1000.50,
            'currency' => 'PLN'
        ];

        $invoice = $this->service->create($data);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('INV/TEST/001', $invoice->invoice_number);
        $this->assertDatabaseHas('invoices', ['invoice_number' => 'INV/TEST/001']);
    }

    public function test_can_get_all_invoices(): void
    {
        Invoice::factory()->count(3)->create();

        $invoices = $this->service->getAll();

        $this->assertCount(3, $invoices);
    }
}
