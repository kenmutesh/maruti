<?php

namespace Database\Seeders;

use App\Enums\PurchaseOrderStatusEnum;
use App\Models\Company;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Seeder;

class PurchaseOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['purchaseorders', 'powders', 'inventoryitems', 'noninventoryitems'])->get();
        foreach ($companies as $company) {
            foreach($company->purchaseorders as $purchaseorder){
                if($purchaseorder->status == PurchaseOrderStatusEnum::CLOSED->value){
                    PurchaseOrderItem::factory()->powder()->create([
                        'purchase_order_id' => $purchaseorder->id,
                        'powder_id' => $company->powders[0]->id,
                    ]);
                    PurchaseOrderItem::factory()->inventoryitem()->create([
                        'purchase_order_id' => $purchaseorder->id,
                        'inventory_item_id' => $company->inventoryitems[0]->id
                    ]);
                    PurchaseOrderItem::factory()->noninventoryitem()->create([
                        'purchase_order_id' => $purchaseorder->id,
                        'non_inventory_item_id' => $company->noninventoryitems[0]->id,
                    ]);
                }else{
                    PurchaseOrderItem::factory()->newitempowder()->create([
                        'purchase_order_id' => $purchaseorder->id,
                        'powder_id' => $company->powders[0]->id,
                    ]);
                    PurchaseOrderItem::factory()->newiteminventory()->create([
                        'purchase_order_id' => $purchaseorder->id,
                    ]);
                    PurchaseOrderItem::factory()->newitemnoninventory()->create([
                        'purchase_order_id' => $purchaseorder->id,
                    ]);
                }

            }
        }
    }
}
