<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_clinics(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create(['name' => 'عيادة العظام']);

        $response = $this->get(route('clinics.index'));

        $response->assertStatus(200);
        $response->assertSee('عيادة العظام');
    }

    public function test_can_create_clinic_with_valid_data(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('clinics.store'), [
            'name' => 'عيادة العيون',
            'status' => 'نشطة',
            'color' => '#3b82f6',
            'code' => 'EYE-01',
            'daily_quota' => 25,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clinics', [
            'name' => 'عيادة العيون',
            'code' => 'EYE-01',
            'status' => 'نشطة',
        ]);
    }

    public function test_creating_clinic_auto_generates_code_if_not_provided(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('clinics.store'), [
            'name' => 'عيادة الأطفال',
            'status' => 'نشطة',
        ]);

        $response->assertRedirect();
        $clinic = Clinic::where('name', 'عيادة الأطفال')->first();
        $this->assertNotNull($clinic);
        $this->assertNotEmpty($clinic->code);
    }

    public function test_can_view_clinic_details(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create(['name' => 'عيادة الجلدية']);

        $response = $this->get(route('clinics.show', $clinic->id));

        $response->assertStatus(200);
        $response->assertSee('عيادة الجلدية');
    }

    public function test_can_update_clinic(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create(['name' => 'عيادة قديمة']);

        $response = $this->put(route('clinics.update', $clinic->id), [
            'name' => 'عيادة محدثة',
            'status' => 'نشطة',
            'color' => '#10b981',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clinics', [
            'id' => $clinic->id,
            'name' => 'عيادة محدثة',
        ]);
    }

    public function test_can_archive_and_restore_clinic(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create();

        // Archive (Soft delete)
        $response = $this->delete(route('clinics.destroy', $clinic->id));
        $response->assertRedirect();
        $this->assertSoftDeleted('clinics', ['id' => $clinic->id]);

        // Restore
        $restoreResponse = $this->post(route('clinics.restore', $clinic->id));
        $restoreResponse->assertRedirect();
        $this->assertNotSoftDeleted('clinics', ['id' => $clinic->id]);
    }

    public function test_can_assign_doctor_to_clinic(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create();
        $doctor = User::factory()->create();

        $response = $this->post(route('clinics.assign_doctor', $clinic->id), [
            'doctor_id' => $doctor->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $doctor->id,
            'clinic_id' => $clinic->id,
        ]);
    }
}
