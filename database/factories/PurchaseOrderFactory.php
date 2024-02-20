<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatusEnum;
use App\Models\Bin;
use App\Models\Company;
use App\Models\Floor;
use App\Models\PurchaseOrder;
use App\Models\Shelf;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    public function definition()
    {
        $purchaseOrder = new PurchaseOrder();

        return [
            'lpo_prefix' => $purchaseOrder->next_purchase_order_prefix,
            'lpo_suffix' => $purchaseOrder->next_purchase_order_suffix,
            'record_date' => $this->faker->dateTimeBetween('+1 days', '+5 days'),
            'due_date' => $this->faker->dateTimeBetween('+10 days', '+30 days'),
            'quotation_ref' => $this->faker->numberBetween(1000, 9000),
            'memo_ref' => $this->faker->numberBetween(1000, 9000),
            'invoice_ref' => $this->faker->numberBetween(1000, 9000),
            'delivery_ref' => $this->faker->numberBetween(1000, 9000),
            'currency' => 'KES',
            'terms' => $this->faker->sentence(20),
            'status' => PurchaseOrderStatusEnum::OPEN->value,
            'supplier_id' => Supplier::factory(),
            'company_id' => Company::factory(),
        ];
    }

    public function cancelledpurchaseorder()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PurchaseOrderStatusEnum::CANCELLED->value,
            ];
        });
    }

    public function closedpurchaseorder()
    {
        return $this->state(function (array $attributes) {
            $bin = Bin::orderBy('id', 'desc')->with(['shelf.floor.warehouse'])->first();
            return [
                'status' => PurchaseOrderStatusEnum::CLOSED->value,
                'invoice_ref' => $this->faker->regexify('[A-Z]{5}[0-4]{3}'),
                'delivery_ref' => $this->faker->regexify('[A-Z]{5}[0-4]{3}'),
                'warehouse_id' => $bin->shelf->floor->warehouse_id,
                'floor_id' => $bin->shelf->floor_id,
                'shelf_id' => $bin->shelf_id,
                'bin_id' => $bin->id
            ];
        });
    }
}
