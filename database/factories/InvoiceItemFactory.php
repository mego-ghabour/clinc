<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'service_id' => Service::factory(),
            'description' => fake()->sentence(),
            'quantity' => 1,
            'unit_price' => 100.00,
            'total' => 100.00,
        ];
    }
}
