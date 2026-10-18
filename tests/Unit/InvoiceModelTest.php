<?php

namespace Tests\Unit;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_balance_accessor(): void
    {
        $invoice = Invoice::factory()->create([
            'total' => 500.00,
            'paid_amount' => 200.00,
        ]);

        $this->assertEquals(300.00, (float)$invoice->balance);
    }

    public function test_invoice_number_is_automatically_generated(): void
    {
        $invoice = Invoice::factory()->create(['invoice_number' => null]);

        $this->assertNotNull($invoice->invoice_number);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
    }
}
