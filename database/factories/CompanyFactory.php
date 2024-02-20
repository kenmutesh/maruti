<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatusEnum;
use App\Models\AprotecUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    public function definition()
    {
        return [
            'name'  => strtoupper($this->faker->unique()->firstName()). " COMPANY",
            'email' => strtoupper($this->faker->unique()->safeEmail()),
            'subscription_status' => SubscriptionStatusEnum::INCOMPLETE,
            'subscription_start_date' => Carbon::now(),
            'subscription_duration' => rand(30, 90),
            'activation_key' => Str::random(8),
            'created_by' =>  AprotecUser::factory()
        ];
    }

    // TODO:: Factory method to test the various types of subscriptions created
}
