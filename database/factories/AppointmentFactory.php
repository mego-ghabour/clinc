<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'service' => 'كشف عام',
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'status' => 'مجدول',
            'notes' => fake()->sentence(),
        ];
    }
}
