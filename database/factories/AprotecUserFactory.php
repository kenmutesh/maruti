<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AprotecUserFactory extends Factory
{
    public function definition()
    {
        return [
            'email' => strtoupper($this->faker->unique()->safeEmail()),
            'username' => strtoupper($this->faker->unique()->firstName()),
            'password' => Hash::make('12345678'),
        ];
    }

    public function validpassworedreset()
    {
        return $this->state(function (array $attributes) {
            return [
                'reset_token' => Str::random(10)
            ];
        });
    }

    // TODO: Comment on whether to remove this state because it is default
    public function invalidpassworedreset()
    {
        return $this->state(function (array $attributes) {
            return [
                'reset_token' => null
            ];
        });
    }
}
