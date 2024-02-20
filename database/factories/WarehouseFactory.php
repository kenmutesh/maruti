<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    public function definition()
    {
        return [
            'warehouse_name' => $this->faker->city(),
            'warehouse_description' => $this->faker->sentence(20),
            'location_id' => Location::factory(),
            'company_id' => Company::factory()
        ];
    }
}
