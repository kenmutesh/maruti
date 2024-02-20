<?php

namespace Database\Factories;

use App\Enums\InventoryItemsEnum;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    public function definition()
    {
        $cost = $this->faker->numberBetween(100, 1000);
        $price = $this->faker->numberBetween($cost, ($cost+100));
        return [
            'item_name' => $this->faker->regexify('[A-Z]{9}'),
            'item_code' => $this->faker->regexify('[A-Z]{4}[0-9]{3}'),
            'item_description' => $this->faker->sentence(10),
            'serial_no' => $this->faker->regexify('[A-Z]{9}[0-9]{5}'),
            'quantity_tag' => "UNITS",
            'goods_weight' => $this->faker->randomFloat(3, 10, 40),
            'standard_cost' => $cost,
            'standard_cost_vat' => $this->faker->numberBetween(0, 90),
            'standard_price' => $price,
            'standard_price_vat' => $this->faker->numberBetween(0, 90),
            'min_threshold' => 10,
            'max_threshold' => 100,
            'current_quantity' => 20,
            'opening_quantity' => 20,
            'supplier_id' => Supplier::factory(),
            'company_id' => Company::factory()
        ];
    }

    public function aluminium(){
        return $this->state(function (array $attributes) {
            return [
                'type' => InventoryItemsEnum::ALUMINIUM->value
            ];
        });
    }

    public function hardware(){
        return $this->state(function (array $attributes) {
            return [
                'type' => InventoryItemsEnum::HARDWARE->value
            ];
        });
    }
}
