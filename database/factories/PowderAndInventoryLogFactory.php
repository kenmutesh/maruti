<?php

namespace Database\Factories;

use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\Company;
use App\Models\Floor;
use App\Models\PurchaseOrder;
use App\Models\Shelf;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class PowderAndInventoryLogFactory extends Factory
{
    public function definition()
    {
        return [
            'sum_added' => $this->faker->numberBetween(10, 100),
            'warehouse_id' => Warehouse::factory(),
            'floor_id' => Floor::factory(),
            'shelf_id' => Shelf::factory(),
            'bin_id' => Bin::factory(),
            'company_id' => Company::factory()
        ];
    }

    public function purchaseorder()
    {
        return $this->state(function (array $attributes) {

            return [
                'reason' => PowderAndInventoryLogsEnum::PURCHASEORDER->value,
                'reason_id' => PurchaseOrder::factory()->closedpurchaseorder()->create(),
                'warehouse_id' => Warehouse::factory(),
                'floor_id' => Floor::factory(),
                'shelf_id' => Shelf::factory(),
                'bin_id' => Bin::factory()
            ];
        });
    }
}
