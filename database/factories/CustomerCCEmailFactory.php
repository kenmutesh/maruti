<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerCCEmailFactory extends Factory
{
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'email' => strtoupper($this->faker->unique()->email())
        ];
    }
}
