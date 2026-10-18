<?php

namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_patients(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create([
            'first_name' => 'خالد',
            'family_name' => 'الغامدي',
        ]);

        $response = $this->get(route('patients.index'));

        $response->assertStatus(200);
        $response->assertSee('خالد');
        $response->assertSee('الغامدي');
    }

    public function test_can_search_patients_by_query(): void
    {
        $this->authenticateSuperAdmin();
        $p1 = Patient::factory()->create([
            'first_name' => 'فيصل',
            'family_name' => 'العتيبي',
            'id_number' => '1099887766',
        ]);
        $p2 = Patient::factory()->create([
            'first_name' => 'سارة',
            'family_name' => 'الدوسري',
            'id_number' => '1022334455',
        ]);

        $response = $this->get(route('patients.index', ['search' => 'فيصل']));
        $response->assertStatus(200);
        $response->assertSee('فيصل');
        $response->assertDontSee('سارة');
    }

    public function test_can_create_patient_with_valid_data(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('patients.store'), [
            'first_name' => 'محمد',
            'father_name' => 'علي',
            'family_name' => 'القحطاني',
            'id_number' => '1055555555',
            'mobile' => '0512345678',
            'gender' => 'ذكر',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'first_name' => 'محمد',
            'family_name' => 'القحطاني',
            'id_number' => '1055555555',
        ]);

        $patient = Patient::where('id_number', '1055555555')->first();
        $this->assertNotNull($patient->mrn);
    }

    public function test_cannot_create_patient_with_duplicate_id_number(): void
    {
        $this->authenticateSuperAdmin();
        Patient::factory()->create(['id_number' => '1012345678']);

        $response = $this->post(route('patients.store'), [
            'first_name' => 'أحمد',
            'family_name' => 'الشهري',
            'id_number' => '1012345678',
        ]);

        $response->assertSessionHasErrors('id_number');
    }

    public function test_can_view_patient_details(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create([
            'first_name' => 'عبدالله',
            'family_name' => 'الشمري',
        ]);

        $response = $this->get(route('patients.show', $patient->id));

        $response->assertStatus(200);
        $response->assertSee('عبدالله');
        $response->assertSee('الشمري');
    }

    public function test_can_update_patient(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create([
            'first_name' => 'سعد',
            'family_name' => 'المالكي',
            'mobile' => '0500000001',
        ]);

        $response = $this->put(route('patients.update', $patient->id), [
            'first_name' => 'سعد',
            'family_name' => 'المالكي',
            'mobile' => '0599999999',
            'id_number' => $patient->id_number,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'mobile' => '0599999999',
        ]);
    }

    public function test_can_archive_and_restore_patient(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();

        // Archive
        $deleteResponse = $this->delete(route('patients.destroy', $patient->id));
        $deleteResponse->assertRedirect();
        $this->assertSoftDeleted('patients', ['id' => $patient->id]);

        // Restore
        $restoreResponse = $this->post(route('patients.restore', $patient->id));
        $restoreResponse->assertRedirect();
        $this->assertNotSoftDeleted('patients', ['id' => $patient->id]);
    }

    public function test_patient_search_api_returns_json_results(): void
    {
        $this->authenticateSuperAdmin();
        Patient::factory()->create([
            'first_name' => 'سليمان',
            'family_name' => 'الحربي',
            'mrn' => 'PAT-123456',
        ]);

        $response = $this->getJson(route('patients.search', ['q' => 'سليمان']));

        $response->assertStatus(200);
        $response->assertJsonFragment(['first_name' => 'سليمان', 'family_name' => 'الحربي']);
    }
}
