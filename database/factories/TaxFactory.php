<?php

namespace Database\Factories;

use App\Enums\TaxTypesEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    public function definition()
    {
        return [
            'percentage' => $this->faker->numberBetween(0, 100),
            'type' => TaxTypesEnum::VAT,
            'company_id' => Company::factory()
        ];
    }
}
