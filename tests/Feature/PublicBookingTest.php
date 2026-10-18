<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_booking_portal_renders_successfully(): void
    {
        Clinic::factory()->create(['status' => 'نشطة', 'name' => 'عيادة الأسنان']);

        $response = $this->get(route('booking.index'));

        $response->assertStatus(200);
        $response->assertSee('عيادة الأسنان');
    }

    public function test_public_booking_displays_closed_view_when_disabled(): void
    {
        Setting::updateOrCreate(
            ['key' => 'public_booking_enabled'],
            ['value' => '0', 'type' => 'text']
        );

        $response = $this->get(route('booking.index'));

        $response->assertStatus(200);
        $response->assertSee('بوابة الحجز مغلقة مؤقتاً');
    }

    public function test_can_fetch_clinic_doctors_via_public_api(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $clinic = Clinic::factory()->create(['status' => 'نشطة']);
        $doctor = User::factory()->create([
            'name' => 'د. فهد السديري',
            'clinic_id' => $clinic->id,
            'status' => 'نشط',
        ]);
        $doctor->assignRole('طبيب');

        $response = $this->getJson("/api/public/clinics/{$clinic->id}/doctors");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'د. فهد السديري']);
    }

    public function test_can_fetch_available_doctor_slots_via_public_api(): void
    {
        $doctor = User::factory()->create(['consultation_duration' => 30]);

        // Next Sunday date
        $sunday = now()->next(\Carbon\Carbon::SUNDAY)->format('Y-m-d');

        // Create schedule for Sunday (day_of_week = 0)
        DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 0,
            'is_working' => true,
            'shifts' => [
                ['start' => '10:00', 'end' => '11:00']
            ],
            'consultation_duration' => 30,
        ]);

        $response = $this->getJson("/api/public/doctors/{$doctor->id}/slots?date={$sunday}");

        $response->assertStatus(200);
        $slots = $response->json();
        $this->assertContains('10:00', $slots);
        $this->assertContains('10:30', $slots);
    }

    public function test_can_submit_public_booking_and_creates_patient_and_appointment(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $clinic = Clinic::factory()->create();
        $doctor = User::factory()->create(['clinic_id' => $clinic->id]);
        $doctor->assignRole('طبيب');

        $response = $this->postJson(route('booking.store'), [
            'first_name' => 'ريان',
            'last_name' => 'البقمي',
            'phone' => '0566778899',
            'id_type' => 'هوية وطنية',
            'id_number' => '1077665544',
            'gender' => 'ذكر',
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctor->id,
            'date' => '2026-11-15',
            'time' => '10:30',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify patient created
        $patient = Patient::where('id_number', '1077665544')->first();
        $this->assertNotNull($patient);
        $this->assertEquals('ريان', $patient->first_name);
        $this->assertEquals('البقمي', $patient->family_name);
        $this->assertEquals('0566778899', $patient->mobile);

        // Verify appointment created
        $appointment = Appointment::where('patient_id', $patient->id)->first();
        $this->assertNotNull($appointment);
        $this->assertEquals($doctor->id, $appointment->doctor_id);
        $this->assertEquals('مجدول', $appointment->status);
        $this->assertEquals('كشف عام', $appointment->service);
    }

    public function test_public_booking_submission_rejected_when_disabled(): void
    {
        Setting::updateOrCreate(
            ['key' => 'public_booking_enabled'],
            ['value' => '0', 'type' => 'text']
        );

        $response = $this->postJson(route('booking.store'), []);

        $response->assertStatus(403);
    }
}
