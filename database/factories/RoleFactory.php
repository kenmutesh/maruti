<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => strtoupper($this->faker->unique()->firstName()),
            'privileges' => json_encode([]),
            'company_id' => Company::factory()
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'ADMIN',
            ];
        });
    }
}
