<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition()
    {
        return [
            'location_name' => $this->faker->unique()->city(),
            'location_description' => $this->faker->sentence(20),
            'company_id' => Company::factory()
        ];
    }
}
