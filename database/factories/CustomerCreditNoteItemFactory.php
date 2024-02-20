<?php

namespace Database\Factories;

use App\Models\CustomerCreditNote;
use App\Models\InventoryItem;
use App\Models\Powder;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerCreditNoteItemFactory extends Factory
{
    public function definition()
    {
        return [
            'customer_credit_note_id' => CustomerCreditNote::factory(),
            'unit_price' => $this->faker->numberBetween(100, 1000),
            'quantity' => $this->faker->numberBetween(10, 100),
            'vat' => $this->faker->numberBetween(0, 90)
        ];
    }

    public function powder(){
        return $this->state(function (array $attributes) {
            return [
                'powder_id' => Powder::factory(),
            ];
        });
    }

    public function inventoryitem(){
        return $this->state(function (array $attributes) {
            return [
                'inventory_item_id' => InventoryItem::factory(),
            ];
        });
    }

    public function customitem(){
        return $this->state(function (array $attributes) {
            return [
                'custom_item_name' => $this->faker->regexify('[A-Z]{9}'),
            ];
        });
    }

}
