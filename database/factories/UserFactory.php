<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => strtoupper($this->faker->unique()->firstName()),
            'email' => strtoupper($this->faker->unique()->safeEmail()),
            'password' => Hash::make('12345678'),
            'role_id' => Role::factory(),
            'company_id' => Company::factory()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => Carbon::now(),
            ];
        });
    }
}
