<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\WhatsAppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_test_whatsapp_message(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('whatsapp.test'), [
            'phone' => '0501234567',
            'message' => 'رسالة تجريبية لفحص بوابة الواتساب',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('whatsapp_notifications', [
            'recipient_phone' => '966501234567',
            'type' => 'test',
        ]);
    }

    public function test_can_send_appointment_whatsapp_reminder(): void
    {
        $this->authenticateSuperAdmin();

        $patient = Patient::factory()->create(['mobile' => '0551122334']);
        $doctor = User::factory()->create(['role' => 'طبيب', 'status' => 'نشط']);
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service' => 'كشف أطفال',
            'appointment_date' => '2026-11-10',
            'appointment_time' => '16:00',
            'status' => 'مجدول',
        ]);

        $response = $this->post(route('appointments.whatsapp', $appointment->id), [
            'type' => 'reminder',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('whatsapp_notifications', [
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'type' => 'reminder',
        ]);
    }

    public function test_can_view_whatsapp_logs(): void
    {
        $this->authenticateSuperAdmin();

        WhatsAppNotification::create([
            'recipient_phone' => '966500000000',
            'type' => 'booking_confirmation',
            'message' => 'تم تأكيد موعدك بنجاح',
            'status' => 'sent',
        ]);

        $response = $this->get(route('whatsapp.logs'));
        $response->assertStatus(200);
        $response->assertSee('966500000000');
        $response->assertSee('تم تأكيد موعدك بنجاح');
    }

    public function test_can_connect_whatsapp_via_qr(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('whatsapp.qr.connect'), [
            'phone' => '966551122334',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'whatsapp_connection_type',
            'value' => 'qr',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'whatsapp_qr_status',
            'value' => 'connected',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'whatsapp_connected_phone',
            'value' => '966551122334',
        ]);
    }

    public function test_can_disconnect_whatsapp_qr(): void
    {
        $this->authenticateSuperAdmin();

        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'connected']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_phone'], ['value' => '966551122334']);

        $response = $this->post(route('whatsapp.qr.disconnect'));

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'whatsapp_qr_status',
            'value' => 'disconnected',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'whatsapp_connected_phone',
            'value' => '',
        ]);
    }

    public function test_can_check_whatsapp_status(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->get(route('whatsapp.status.check'));
        $response->assertRedirect();
    }

    public function test_can_get_local_qr_endpoint(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->get(route('whatsapp.local.qr'));
        $response->assertStatus(200);
        $this->assertArrayHasKey('status', $response->json());
    }

    public function test_can_check_local_status_endpoint(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->getJson(route('whatsapp.local.status'));
        $response->assertStatus(200);
    }

    public function test_can_logout_from_local_whatsapp(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('whatsapp.local.logout'));
        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'whatsapp_qr_status',
            'value' => 'disconnected',
        ]);
    }
}
