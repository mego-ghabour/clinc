<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoubleBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_book_overlapping_appointment_for_same_doctor(): void
    {
        $this->authenticateSuperAdmin();

        $patient1 = Patient::factory()->create();
        $patient2 = Patient::factory()->create();
        $doctor = User::factory()->create(['role' => 'طبيب', 'status' => 'نشط']);

        // First booking succeeds
        Appointment::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'service' => 'كشف عام',
            'appointment_date' => '2026-11-01',
            'appointment_time' => '10:00',
            'status' => 'مجدول',
        ]);

        // Second booking for the same doctor, date and time must be rejected
        $response = $this->post(route('appointments.store'), [
            'patient_id' => $patient2->id,
            'doctor_id' => $doctor->id,
            'service' => 'كشف عام',
            'appointment_date' => '2026-11-01',
            'appointment_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('appointment_time');
        $this->assertEquals(1, Appointment::where('doctor_id', $doctor->id)->where('appointment_date', '2026-11-01')->where('appointment_time', '10:00')->count());
    }

    public function test_public_booking_rejects_double_booking(): void
    {
        $clinic = Clinic::factory()->create(['status' => 'نشطة']);
        $doctor = User::factory()->create(['role' => 'طبيب', 'status' => 'نشط', 'clinic_id' => $clinic->id]);
        $patient = Patient::factory()->create();

        // Existing booking
        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service' => 'كشف عام',
            'appointment_date' => '2026-11-05',
            'appointment_time' => '11:00',
            'status' => 'مجدول',
        ]);

        // Attempting to book the same slot via public booking
        $response = $this->postJson(route('booking.store'), [
            'first_name' => 'سارة',
            'last_name' => 'الغامدي',
            'phone' => '0501234567',
            'id_type' => 'هوية وطنية',
            'id_number' => '1099887766',
            'gender' => 'أنثى',
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'date' => '2026-11-05',
            'time' => '11:00',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }
}
