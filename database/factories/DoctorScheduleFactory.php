<?php

namespace Database\Factories;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorScheduleFactory extends Factory
{
    protected $model = DoctorSchedule::class;

    public function definition(): array
    {
        return [
            'doctor_id' => User::factory(),
            'day_of_week' => 0, // Sunday
            'is_working' => true,
            'shifts' => [
                ['start' => '09:00', 'end' => '17:00']
            ],
            'consultation_duration' => 20,
        ];
    }
}
