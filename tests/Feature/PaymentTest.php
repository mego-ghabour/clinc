<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_partial_payment_on_invoice(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create([
            'total' => 200.00,
            'paid_amount' => 0.00,
            'status' => 'unpaid',
        ]);

        $response = $this->post(route('payments.store', $invoice->id), [
            'amount' => 100.00,
            'payment_method' => 'مدى (بطاقة)',
            'reference_number' => 'AUTH-12345',
        ]);

        $response->assertRedirect();
        $invoice->refresh();

        $this->assertEquals(100.00, (float)$invoice->paid_amount);
        $this->assertEquals('partially_paid', $invoice->status);
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount' => 100.00,
            'payment_method' => 'مدى (بطاقة)',
        ]);
    }

    public function test_recording_full_payment_marks_invoice_as_paid(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create([
            'total' => 150.00,
            'paid_amount' => 0.00,
            'status' => 'unpaid',
        ]);

        $response = $this->post(route('payments.store', $invoice->id), [
            'amount' => 150.00,
            'payment_method' => 'نقدي',
        ]);

        $response->assertRedirect();
        $invoice->refresh();

        $this->assertEquals(150.00, (float)$invoice->paid_amount);
        $this->assertEquals('paid', $invoice->status);
    }

    public function test_cannot_pay_more_than_remaining_invoice_balance(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create([
            'total' => 100.00,
            'paid_amount' => 50.00,
        ]);

        // Remaining balance is 50, but trying to pay 80
        $response = $this->post(route('payments.store', $invoice->id), [
            'amount' => 80.00,
            'payment_method' => 'نقدي',
        ]);

        $response->assertSessionHasErrors('amount');
        $invoice->refresh();
        $this->assertEquals(50.00, (float)$invoice->paid_amount);
    }

    public function test_can_delete_payment_and_reverts_invoice_balance(): void
    {
        $this->authenticateSuperAdmin();
        $invoice = Invoice::factory()->create([
            'total' => 200.00,
            'paid_amount' => 200.00,
            'status' => 'paid',
        ]);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 200.00,
        ]);

        $response = $this->delete(route('payments.destroy', $payment->id));

        $response->assertRedirect();
        $invoice->refresh();

        $this->assertEquals(0.00, (float)$invoice->paid_amount);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }
}
