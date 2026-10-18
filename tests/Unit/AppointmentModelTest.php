<?php

namespace Tests\Unit;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_number_is_automatically_generated(): void
    {
        $appointment = Appointment::factory()->create(['appointment_number' => null]);

        $this->assertNotNull($appointment->appointment_number);
        $this->assertStringStartsWith('APT-', $appointment->appointment_number);
    }
}
