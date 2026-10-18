<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_invoices(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('invoices.index'));

        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
    }

    public function test_can_view_invoice_create_page(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->get(route('invoices.create'));

        $response->assertStatus(200);
    }

    public function test_can_create_invoice_with_items_and_tax_calculation(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $doctor = User::factory()->create();
        $service = Service::factory()->create(['price' => 100.00]);

        $response = $this->post(route('invoices.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'issue_date' => date('Y-m-d'),
            'discount' => 10.00,
            'items' => [
                [
                    'service_id' => $service->id,
                    'quantity' => 2,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        $invoice = Invoice::where('patient_id', $patient->id)->first();
        $this->assertNotNull($invoice);
        $response->assertRedirect(route('invoices.show', $invoice->id));

        // Subtotal = 2 * 100 = 200
        // Discount = 10
        // Taxable = 200 - 10 = 190
        // Tax (15%) = 190 * 0.15 = 28.50
        // Total = 190 + 28.50 = 218.50
        $this->assertEquals(200.00, (float)$invoice->subtotal);
        $this->assertEquals(10.00, (float)$invoice->discount);
        $this->assertEquals(28.50, (float)$invoice->tax);
        $this->assertEquals(218.50, (float)$invoice->total);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertNotEmpty($invoice->invoice_number);
        $this->assertCount(1, $invoice->items);
    }

    public function test_can_view_invoice_details(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('invoices.show', $invoice->id));

        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
    }

    public function test_can_delete_invoice(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'بند تجريبي',
            'quantity' => 1,
            'unit_price' => 50,
            'total' => 50,
        ]);

        $response = $this->delete(route('invoices.destroy', $invoice->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }
}
