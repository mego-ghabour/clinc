<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_successfully_with_live_kpis(): void
    {
        $this->authenticateSuperAdmin();

        $patient = Patient::factory()->create();
        $doctor = User::factory()->create(['role' => 'طبيب', 'status' => 'نشط']);
        $doctor->assignRole('طبيب');

        $appointment = Appointment::create([
            'appointment_number' => 'APT-00001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service' => 'كشف عام',
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'status' => 'تم الحضور',
        ]);

        $invoice = Invoice::create([
            'patient_id' => $patient->id,
            'issue_date' => now(),
            'subtotal' => 200,
            'discount' => 0,
            'tax' => 30,
            'total' => 230,
            'paid_amount' => 100,
            'status' => 'مدفوعة جزئياً',
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => 100,
            'payment_method' => 'نقداً (Cash)',
            'payment_date' => now(),
        ]);

        LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عاجل',
            'status' => 'قيد التحليل',
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('لوحة مؤشرات الأداء الحية والتقارير المالية');
        $response->assertSee('مواعيد اليوم');
        $response->assertSee('المحصل اليوم');
        $response->assertSee('التحاليل والأشعة النشطة');
        $response->assertSee('مسار الإيرادات والمواعيد اليومية');
        $response->assertSee('توزيع طرق الدفع والتحصيل');
    }
}
