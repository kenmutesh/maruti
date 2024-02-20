<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Shelf;
use Illuminate\Database\Eloquent\Factories\Factory;

class BinFactory extends Factory
{
    public function definition()
    {
        return [
            'bin_name' => $this->faker->city(),
            'bin_description' => $this->faker->sentence(20),
            'shelf_id' => Shelf::factory(),
            'company_id' => Company::factory()
        ];
    }
}
