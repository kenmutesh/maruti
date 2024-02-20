<?php

namespace Database\Factories;

use App\Enums\PaymentModesEnum;
use App\Models\Company;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'payment_mode' => PaymentModesEnum::CASH->value,
            'payment_date' => Carbon::now(),
            'transaction_ref' => $this->faker->regexify('[A-Z]{5}[0-4]{3}'),
            'company_id' => Company::factory()
        ];
    }

    public function nullified()
    {
        return $this->state(function (array $attributes) {
            return [
                'nullified_at' => Carbon::now()
            ];
        });
    }
}
