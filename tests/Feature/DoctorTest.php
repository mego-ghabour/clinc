<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_doctors(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create([
            'name' => 'د. ياسر المنصور',
            'status' => 'نشط',
        ]);
        $doctor->assignRole('طبيب');

        $response = $this->get(route('doctors.index'));

        $response->assertStatus(200);
        $response->assertSee('د. ياسر المنصور');
    }

    public function test_can_filter_doctors_by_status(): void
    {
        $this->authenticateSuperAdmin();
        $presentDoctor = User::factory()->create([
            'name' => 'د. حاضر',
            'status' => 'نشط',
            'is_present' => true,
        ]);
        $presentDoctor->assignRole('طبيب');

        $absentDoctor = User::factory()->create([
            'name' => 'د. غائب',
            'status' => 'نشط',
            'is_present' => false,
        ]);
        $absentDoctor->assignRole('طبيب');

        $response = $this->get(route('doctors.index', ['status' => 'present']));
        $response->assertStatus(200);
        $response->assertSee('د. حاضر');
        $response->assertDontSee('د. غائب');
    }

    public function test_can_create_new_doctor(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create();

        $response = $this->post(route('doctors.store'), [
            'name' => 'د. طارق العمري',
            'email' => 'tariq@hospital.com',
            'phone' => '0501234567',
            'clinic_id' => $clinic->id,
            'specialization' => 'طب باطني',
            'license_number' => 'LIC-9988',
            'consultation_duration' => 20,
        ]);

        $response->assertRedirect();
        $doctor = User::where('email', 'tariq@hospital.com')->first();
        $this->assertNotNull($doctor);
        $this->assertTrue($doctor->hasRole('طبيب'));
        $this->assertEquals('نشط', $doctor->status);
        $this->assertTrue($doctor->is_present);
    }

    public function test_can_view_doctor_details(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create(['name' => 'د. هاني']);
        $doctor->assignRole('طبيب');

        $response = $this->get(route('doctors.show', $doctor->id));

        $response->assertStatus(200);
        $response->assertSee('د. هاني');
    }

    public function test_can_update_doctor(): void
    {
        $this->authenticateSuperAdmin();
        $clinic = Clinic::factory()->create();
        $doctor = User::factory()->create(['clinic_id' => $clinic->id]);
        $doctor->assignRole('طبيب');

        $response = $this->put(route('doctors.update', $doctor->id), [
            'name' => 'د. ماجد المحدث',
            'clinic_id' => $clinic->id,
            'specialization' => 'طب أسرة',
            'consultation_duration' => 25,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $doctor->id,
            'name' => 'د. ماجد المحدث',
            'specialization' => 'طب أسرة',
        ]);
    }

    public function test_can_toggle_doctor_account_status(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create(['status' => 'نشط']);
        $doctor->assignRole('طبيب');

        $response = $this->patch(route('doctors.toggleAccountStatus', $doctor->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $doctor->id, 'status' => 'موقوف']);

        $response = $this->patch(route('doctors.toggleAccountStatus', $doctor->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $doctor->id, 'status' => 'نشط']);
    }

    public function test_can_toggle_doctor_presence_status(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create(['is_present' => true]);
        $doctor->assignRole('طبيب');

        $response = $this->patch(route('doctors.toggleStatus', $doctor->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $doctor->id, 'is_present' => false]);

        $response = $this->patch(route('doctors.toggleStatus', $doctor->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $doctor->id, 'is_present' => true]);
    }
}
