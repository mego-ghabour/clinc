<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'father_name' => fake()->firstNameMale(),
            'family_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['ذكر', 'أنثى']),
            'date_of_birth' => fake()->date('Y-m-d', '-20 years'),
            'id_type' => 'هوية وطنية',
            'id_number' => fake()->unique()->numerify('10########'),
            'mobile' => fake()->numerify('05########'),
            'nationality' => 'سعودي',
            'city' => 'الرياض',
            'blood_type' => 'O+',
        ];
    }
}
