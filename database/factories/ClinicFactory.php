<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' عيادة',
            'code' => 'CLN-' . fake()->unique()->numerify('###'),
            'color' => '#14b8a6',
            'status' => 'نشطة',
            'notes' => fake()->sentence(),
            'daily_quota' => 20,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'sort_order' => 1,
        ];
    }
}
