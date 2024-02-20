<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition()
    {
        return [
            'supplier_name' => strtoupper($this->faker->unique()->name()),
            'supplier_email' => strtoupper($this->faker->unique()->email()),
            'supplier_mobile' => $this->faker->unique()->phoneNumber(),
            'company_location' => strtoupper($this->faker->city()),
            'company_pin' => 'PIN'.$this->faker->numberBetween(100000, 1000000),
            'company_box' => strtoupper($this->faker->postcode()),
            'company_id' => Company::factory()
        ];
    }
}
