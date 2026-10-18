<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_queues_dashboard(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create(['first_name' => 'منصور']);
        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'appointment_date' => date('Y-m-d'),
            'status' => 'مجدول',
        ]);

        $response = $this->get(route('queues.index'));

        $response->assertStatus(200);
        $response->assertSee('منصور');
    }

    public function test_can_check_in_patient_for_appointment(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $doctor = User::factory()->create();
        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => date('Y-m-d'),
            'status' => 'مجدول',
        ]);

        $response = $this->post(route('queues.checkin', $appointment->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'تم الحضور',
        ]);

        $this->assertDatabaseHas('visits', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'status' => 'قيد الانتظار',
        ]);
    }

    public function test_check_in_does_not_create_duplicate_visit(): void
    {
        $this->authenticateSuperAdmin();
        $appointment = Appointment::factory()->create([
            'appointment_date' => date('Y-m-d'),
            'status' => 'مجدول',
        ]);

        $this->post(route('queues.checkin', $appointment->id));
        $this->post(route('queues.checkin', $appointment->id));

        $count = Visit::where('appointment_id', $appointment->id)->count();
        $this->assertEquals(1, $count);
    }
}
