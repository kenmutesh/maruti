<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition()
    {
        return [
            'customer_name' => strtoupper($this->faker->name()),
            'credit_limit' => $this->faker->numberBetween(100000, 1000000),
            'opening_balance' => $this->faker->numberBetween(1000, 10000),
            'contact_number' => $this->faker->unique()->phoneNumber(),
            'location' => strtoupper($this->faker->city()),
            'company' => strtoupper($this->faker->unique()->name()),
            'contact_person_name' => strtoupper($this->faker->unique()->name()),
            'contact_person_email' => strtoupper($this->faker->unique()->email()),
            'kra_pin' => $this->faker->regexify('[A-Z]{2}[0-4]{8}'),
            'company_id' => Company::factory(),
        ];
    }
}
