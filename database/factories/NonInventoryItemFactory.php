<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class NonInventoryItemFactory extends Factory
{
    public function definition()
    {
        $cost = $this->faker->numberBetween(100, 1000);
        return [
            'item_name' => $this->faker->regexify('[A-Z]{5}'),
            'standard_cost' => $cost,
            'standard_cost_vat' => $this->faker->numberBetween(0, 99),
            'supplier_id' => Supplier::factory(),
            'company_id' => Company::factory()
        ];
    }
}
