<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShelfFactory extends Factory
{
    public function definition()
    {
        return [
            'shelf_name' => $this->faker->city(),
            'floor_id' => Floor::factory(),
            'company_id' => Company::factory()
        ];
    }
}
