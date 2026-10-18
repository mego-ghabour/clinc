<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_visits(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create([
            'first_name' => 'إبراهيم',
            'family_name' => 'النجار',
        ]);
        $visit = Visit::factory()->create(['patient_id' => $patient->id]);

        $response = $this->get(route('visits.index'));

        $response->assertStatus(200);
        $response->assertSee('إبراهيم');
    }

    public function test_can_filter_and_search_visits(): void
    {
        $this->authenticateSuperAdmin();
        $p1 = Patient::factory()->create(['first_name' => 'طارق', 'family_name' => 'السعد']);
        $p2 = Patient::factory()->create(['first_name' => 'نوف', 'family_name' => 'الغامدي']);

        $v1 = Visit::factory()->create(['patient_id' => $p1->id, 'status' => 'جاري الكشف']);
        $v2 = Visit::factory()->create(['patient_id' => $p2->id, 'status' => 'مكتملة']);

        $response = $this->get(route('visits.index', ['status' => 'جاري الكشف']));
        $response->assertStatus(200);
        $visitsData = $response->viewData('visits');
        $this->assertTrue($visitsData->contains('id', $v1->id));
        $this->assertFalse($visitsData->contains('id', $v2->id));
    }

    public function test_can_create_new_visit(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $doctor = User::factory()->create();

        $response = $this->post(route('visits.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_date' => now()->format('Y-m-d H:i:s'),
            'type' => 'كشف جديد',
            'status' => 'قيد الانتظار',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visits', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'type' => 'كشف جديد',
            'status' => 'قيد الانتظار',
        ]);
    }

    public function test_can_view_visit_medical_file(): void
    {
        $this->authenticateSuperAdmin();
        $visit = Visit::factory()->create(['chief_complaint' => 'ألم حاد في الركبة']);

        $response = $this->get(route('visits.show', $visit->id));

        $response->assertStatus(200);
        $response->assertSee('ألم حاد في الركبة');
    }

    public function test_can_update_visit_and_completes_linked_appointment(): void
    {
        $this->authenticateSuperAdmin();
        $appointment = Appointment::factory()->create(['status' => 'تم الحضور']);
        $visit = Visit::factory()->create([
            'appointment_id' => $appointment->id,
            'status' => 'جاري الكشف',
        ]);

        $response = $this->put(route('visits.update', $visit->id), [
            'blood_pressure' => '120/80',
            'temperature' => '37.1',
            'heart_rate' => '75',
            'diagnosis' => 'التهاب أوتار خفيف',
            'treatment_plan' => 'راحة تامة ومسكنات',
            'status' => 'مكتملة',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => 'مكتملة',
            'blood_pressure' => '120/80',
            'diagnosis' => 'التهاب أوتار خفيف',
        ]);

        // Check linked appointment status was updated to 'مكتمل'
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'مكتمل',
        ]);
    }

    public function test_can_delete_visit(): void
    {
        $this->authenticateSuperAdmin();
        $visit = Visit::factory()->create();

        $response = $this->delete(route('visits.destroy', $visit->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('visits', ['id' => $visit->id]);
    }
}
