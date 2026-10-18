<?php

namespace Database\Factories;

use App\Models\RadiologyRequest;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

class RadiologyRequestFactory extends Factory
{
    protected $model = RadiologyRequest::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'visit_id' => Visit::factory(),
            'type' => 'X-Ray',
            'body_part' => 'Chest',
            'clinical_notes' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
