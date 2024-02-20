<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\NonInventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Powder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    public function definition()
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'cost' => $this->faker->numberBetween(100, 1000),
            'quantity' => $this->faker->numberBetween(10, 100),
            'vat' => $this->faker->numberBetween(0, 90)
        ];
    }

    public function newitempowder()
    {
        return $this->state(function (array $attributes) {
            return [
                'item_type' => 'POWDER',
                'new_item_name' => $this->faker->regexify('[A-Z]{9}'),
            ];
        });
    }

    public function powder()
    {
        return $this->state(function (array $attributes) {
            return [
                'powder_id' => Powder::factory(),
                'item_type' => 'POWDER'
            ];
        });
    }

    public function newiteminventory()
    {
        return $this->state(function (array $attributes) {
            return [
                'item_type' => 'ALUMINIUM',
                'new_item_name' => $this->faker->regexify('[A-Z]{9}'),
            ];
        });
    }

    public function inventoryitem()
    {
        return $this->state(function (array $attributes) {
            return [
                'inventory_item_id' => InventoryItem::factory()->aluminium()->create(),
                'item_type' => 'ALUMINIUM'
            ];
        });
    }

    public function newitemnoninventory()
    {
        return $this->state(function (array $attributes) {
            return [
                'item_type' => 'NON INVENTORY',
                'new_item_name' => $this->faker->regexify('[A-Z]{9}'),
            ];
        });
    }

    public function noninventoryitem()
    {
        return $this->state(function (array $attributes) {
            return [
                'non_inventory_item_id' => NonInventoryItem::factory(),
                'item_type' => 'NON INVENTORY'
            ];
        });
    }
}
