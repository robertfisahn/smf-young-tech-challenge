<?php

namespace Database\Factories;

use App\Models\Contractor;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contractor_id' => Contractor::factory(),
            'invoice_number' => 'INV/' . $this->faker->year() . '/' . $this->faker->numberBetween(1000, 9999),
            'date' => $this->faker->date(),
        ];
    }
}
