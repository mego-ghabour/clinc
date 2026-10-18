<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\RadiologyRequest;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RadiologyRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_radiology_requests(): void
    {
        $this->authenticateSuperAdmin();
        $req = RadiologyRequest::factory()->create(['type' => 'MRI الدماغ']);

        $response = $this->get(route('radiology.index'));

        $response->assertStatus(200);
        $response->assertSee('MRI الدماغ');
    }

    public function test_can_create_radiology_request(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $doctor = User::factory()->create();
        $visit = Visit::factory()->create();

        $response = $this->post(route('radiology.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
            'type' => 'X-Ray أشعة سينية',
            'body_part' => 'الصدر',
            'clinical_notes' => 'اشتباه التهاب رئوي',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('radiology_requests', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
            'type' => 'X-Ray أشعة سينية',
            'status' => 'pending',
        ]);
    }

    public function test_radiologist_can_complete_radiology_report(): void
    {
        $user = $this->authenticateSuperAdmin();
        $req = RadiologyRequest::factory()->create(['status' => 'pending']);

        $response = $this->put(route('radiology.update', $req->id), [
            'report_text' => 'لا توجد كسور واضحة، المفاصل سليمة.',
        ]);

        $response->assertRedirect();
        $req->refresh();

        $this->assertEquals('completed', $req->status);
        $this->assertEquals($user->id, $req->radiologist_id);
        $this->assertNotNull($req->completed_at);
        $this->assertStringContainsString('لا توجد كسور', $req->report_text);
    }

    public function test_can_view_radiology_request_details(): void
    {
        $this->authenticateSuperAdmin();
        $req = RadiologyRequest::factory()->create();

        $response = $this->get(route('radiology.show', $req->id));

        $response->assertStatus(200);
    }
}
