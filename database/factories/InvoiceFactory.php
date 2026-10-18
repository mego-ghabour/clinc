<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'subtotal' => 200.00,
            'discount' => 0.00,
            'tax' => 30.00,
            'total' => 230.00,
            'paid_amount' => 0.00,
            'status' => 'unpaid',
            'notes' => fake()->sentence(),
        ];
    }
}
