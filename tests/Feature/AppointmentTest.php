<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_appointments(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create(['first_name' => 'سارة']);
        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'appointment_date' => date('Y-m-d'),
        ]);

        $response = $this->get(route('appointments.index'));

        $response->assertStatus(200);
        $response->assertSee('سارة');
    }

    public function test_can_filter_appointments_by_date_and_doctor(): void
    {
        $this->authenticateSuperAdmin();
        $doctor1 = User::factory()->create(['name' => 'د. خالد']);
        $doctor2 = User::factory()->create(['name' => 'د. سامي']);

        $apt1 = Appointment::factory()->create([
            'doctor_id' => $doctor1->id,
            'appointment_date' => '2026-11-01',
        ]);
        $apt2 = Appointment::factory()->create([
            'doctor_id' => $doctor2->id,
            'appointment_date' => '2026-11-02',
        ]);

        $response = $this->get(route('appointments.index', [
            'date' => '2026-11-01',
            'doctor_id' => $doctor1->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($apt1->appointment_number);
        $response->assertDontSee($apt2->appointment_number);
    }

    public function test_can_book_new_appointment(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $doctor = User::factory()->create();

        $response = $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service' => 'كشف استشاري',
            'appointment_date' => '2026-11-10',
            'appointment_time' => '11:30',
            'notes' => 'مريض يعاني من صداع مستمر',
        ]);

        $response->assertRedirect();
        $appointment = Appointment::where('patient_id', $patient->id)->first();
        $this->assertNotNull($appointment);
        $this->assertEquals('مجدول', $appointment->status);
        $this->assertEquals('كشف استشاري', $appointment->service);
        $this->assertNotEmpty($appointment->appointment_number);
    }

    public function test_booking_validation_requires_all_mandatory_fields(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('appointments.store'), []);

        $response->assertSessionHasErrors([
            'patient_id', 'doctor_id', 'service', 'appointment_date', 'appointment_time'
        ]);
    }

    public function test_can_update_appointment_status(): void
    {
        $this->authenticateSuperAdmin();
        $appointment = Appointment::factory()->create(['status' => 'مجدول']);

        $response = $this->put(route('appointments.update', $appointment->id), [
            'status' => 'مكتمل',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'مكتمل',
        ]);
    }

    public function test_can_delete_appointment(): void
    {
        $this->authenticateSuperAdmin();
        $appointment = Appointment::factory()->create();

        $response = $this->delete(route('appointments.destroy', $appointment->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }
}
