<?php

namespace Tests\Feature;

use App\Models\DoctorLeave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorLeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_doctor_leave(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create();

        $response = $this->post(route('doctor-leaves.store'), [
            'doctor_id' => $doctor->id,
            'from_date' => '2026-11-01',
            'to_date' => '2026-11-05',
            'reason' => 'مؤتمر طبي',
        ]);

        $response->assertRedirect();
        $leave = DoctorLeave::where('doctor_id', $doctor->id)->first();
        $this->assertNotNull($leave);
        $this->assertEquals('2026-11-01', $leave->from_date->format('Y-m-d'));
        $this->assertEquals('2026-11-05', $leave->to_date->format('Y-m-d'));
        $this->assertEquals('مؤتمر طبي', $leave->reason);
    }

    public function test_leave_validation_rejects_to_date_before_from_date(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create();

        $response = $this->post(route('doctor-leaves.store'), [
            'doctor_id' => $doctor->id,
            'from_date' => '2026-11-10',
            'to_date' => '2026-11-05',
        ]);

        $response->assertSessionHasErrors('to_date');
    }

    public function test_can_delete_doctor_leave(): void
    {
        $this->authenticateSuperAdmin();
        $leave = DoctorLeave::factory()->create();

        $response = $this->delete(route('doctor-leaves.destroy', $leave->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('doctor_leaves', ['id' => $leave->id]);
    }
}
