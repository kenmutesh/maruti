<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Warehouse;
use App\Models\Floor;
use App\Models\Shelf;
use App\Models\Bin;
use App\Models\CoatingJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoatingJobMarutiItemFactory extends Factory
{
    public function definition()
    {
        return [
            'coating_job_id' => CoatingJob::factory(),
            'inventory_item_id' => InventoryItem::factory()->aluminium()->create(),
            'warehouse_id' => Warehouse::factory(),
            'floor_id' => Floor::factory(),
            'shelf_id' => Shelf::factory(),
            'bin_id' => Bin::factory(),
            'unit_price' => $this->faker->numberBetween(100, 1000),
            'quantity' => $this->faker->numberBetween(10, 20),
            'vat' => $this->faker->numberBetween(0, 100)
        ];
    }

    public function customitem()
    {
        return $this->state(function (array $attributes) {
            return [
                'custom_item_name' => $this->faker->regexify('[A-Z]{6}'),
                'inventory_item_id' => null,
            ];
        });
    }
}
