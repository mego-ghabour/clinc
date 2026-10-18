<?php

namespace Database\Factories;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'visit_date' => now(),
            'type' => 'كشف جديد',
            'status' => 'قيد الانتظار',
            'chief_complaint' => fake()->sentence(),
            'diagnosis' => fake()->sentence(),
            'treatment_plan' => fake()->sentence(),
            'consultation_fee' => 150.00,
        ];
    }
}
