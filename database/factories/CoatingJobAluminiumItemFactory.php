<?php

namespace Database\Factories;

use App\Models\CoatingJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoatingJobAluminiumItemFactory extends Factory
{
    public function definition()
    {
        return [
            'coating_job_id' => CoatingJob::factory(),
            'item_name' => $this->faker->word(),
            'item_kg' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->numberBetween(100, 1000),
            'quantity' => $this->faker->numberBetween(10, 20),
            'vat' => $this->faker->numberBetween(0, 100)
        ];
    }
}
