<?php

namespace Database\Factories;

use App\Models\DoctorLeave;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorLeaveFactory extends Factory
{
    protected $model = DoctorLeave::class;

    public function definition(): array
    {
        return [
            'doctor_id' => User::factory(),
            'from_date' => now()->format('Y-m-d'),
            'to_date' => now()->addDays(3)->format('Y-m-d'),
            'reason' => 'إجازة سنوية',
        ];
    }
}
