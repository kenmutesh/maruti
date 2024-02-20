<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PowderFactory extends Factory
{
    public function definition()
    {
        $color = 'RAL '. $this->faker->numberBetween(100,900) ." ".$this->faker->colorName();
        $cost = $this->faker->numberBetween(100, 1000);
        $price = $this->faker->numberBetween($cost, ($cost+100));
        return [
            'powder_color' => strtoupper($color),
            'powder_code' => $this->faker->numberBetween(1000, 10000),
            'powder_description' => strtoupper($color),
            'serial_no' => $this->faker->numberBetween(10000, 100000),
            'manufacture_date' => $this->faker->dateTimeBetween('-300 days'),
            'expiry_date' => $this->faker->dateTimeBetween('+10 days', '+300 days'),
            'goods_weight' => $this->faker->randomFloat(3, 1, 6),
            'batch_no' => $this->faker->regexify('[A-Z]{5}[0-4]{3}'),
            'standard_cost' => $cost,
            'standard_cost_vat' => $this->faker->numberBetween(0, 90),
            'standard_price' => $price,
            'standard_price_vat' => $this->faker->numberBetween(0, 90),
            'min_threshold' => 10,
            'max_threshold' => 100,
            'current_weight' => 20,
            'opening_weight' => 20,
            'supplier_id' => Supplier::factory(),
            'company_id' => Company::factory()
        ];
    }
}
