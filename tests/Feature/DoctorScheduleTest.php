<?php

namespace Tests\Feature;

use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_doctor_schedules_index(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create(['name' => 'د. حسام']);
        $doctor->assignRole('طبيب');

        $response = $this->get(route('doctor-schedules.index'));

        $response->assertStatus(200);
        $response->assertSee('د. حسام');
    }

    public function test_can_view_specific_doctor_schedule(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create(['name' => 'د. حسام']);
        $doctor->assignRole('طبيب');

        DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'is_working' => true,
        ]);

        $response = $this->get(route('doctor-schedules.show', $doctor->id));

        $response->assertStatus(200);
        $response->assertSee('د. حسام');
    }

    public function test_can_save_doctor_schedule_shifts(): void
    {
        $this->authenticateSuperAdmin();
        $doctor = User::factory()->create();
        $doctor->assignRole('طبيب');

        $response = $this->post(route('doctor-schedules.store'), [
            'doctor_id' => $doctor->id,
            'schedules' => [
                0 => [
                    'is_working' => 1,
                    'consultation_duration' => 20,
                    'shifts' => [
                        ['start' => '08:00', 'end' => '12:00'],
                        ['start' => '16:00', 'end' => '20:00'],
                    ],
                ],
                1 => [
                    'consultation_duration' => 20,
                    'shifts' => [],
                ]
            ],
        ]);

        $response->assertRedirect();
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', 0)
            ->first();

        $this->assertNotNull($schedule);
        $this->assertTrue($schedule->is_working);
        $this->assertCount(2, $schedule->shifts);
    }
}
