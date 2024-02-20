<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class FloorFactory extends Factory
{
    public function definition()
    {
        return [
            'floor_name' => $this->faker->city(),
            'warehouse_id' => Warehouse::factory(),
            'company_id' => Company::factory()
        ];
    }
}
