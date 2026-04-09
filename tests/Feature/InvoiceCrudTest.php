<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Contractor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_user_cannot_access_invoices(): void
    {
        $response = $this->get(route('invoices.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_see_invoices_list(): void
    {
        $response = $this->actingAs($this->user)->get(route('invoices.index'));
        $response->assertStatus(200);
        $response->assertSee('Lista Faktur');
    }

    public function test_can_store_new_invoice_via_web(): void
    {
        $contractor = Contractor::create([
            'name' => 'ACME',
            'tax_id' => '111',
            'address' => 'USA'
        ]);

        $data = [
            'contractor_id' => $contractor->id,
            'invoice_number' => 'WEB/2026/01',
            'date' => '2026-04-08',
            'total_amount' => 500.00,
            'currency' => 'PLN',
            'payment_method' => 'przelew',
            'items' => [
                ['name' => 'Usługa testowa', 'quantity' => 1, 'unit_price' => 500.00]
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('invoices.store'), $data);

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseHas('invoices', ['invoice_number' => 'WEB/2026/01']);
    }

    public function test_can_delete_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('invoices.destroy', $invoice));

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }
}
