<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'amount' => 100.00,
            'payment_method' => 'نقدي',
            'payment_date' => now()->format('Y-m-d'),
            'reference_number' => 'REF-' . fake()->numerify('#####'),
            'notes' => fake()->sentence(),
        ];
    }
}
