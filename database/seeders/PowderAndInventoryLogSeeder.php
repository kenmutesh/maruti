<?php

namespace Database\Seeders;

use App\Enums\PurchaseOrderStatusEnum;
use App\Models\Company;
use App\Models\PowderAndInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class PowderAndInventoryLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $user = User::where('company_id', $company->id)->first();
            if ($user) {
                Auth::loginUsingId($user->id);

                $purchaseOrders = PurchaseOrder::where('status', PurchaseOrderStatusEnum::CLOSED->value)->get();

                foreach ($purchaseOrders as $purchaseOrder) {
                    foreach ($purchaseOrder->purchaseorderitems as $item) {
                        if ($item->powder_id) {
                            PowderAndInventoryLog::factory()->purchaseorder()->create([
                                'reason_id' => $purchaseOrder->id,
                                'warehouse_id' => $purchaseOrder->warehouse_id,
                                'floor_id' => $purchaseOrder->floor_id,
                                'shelf_id' => $purchaseOrder->shelf_id,
                                'bin_id' => $purchaseOrder->bin_id,
                                'powder_id' => $item->powder_id,
                                'company_id' => $company->id
                            ]);
                        }

                        if ($item->inventory_item_id) {
                            PowderAndInventoryLog::factory()->purchaseorder()->create([
                                'reason_id' => $purchaseOrder->id,
                                'warehouse_id' => $purchaseOrder->warehouse_id,
                                'floor_id' => $purchaseOrder->floor_id,
                                'shelf_id' => $purchaseOrder->shelf_id,
                                'bin_id' => $purchaseOrder->bin_id,
                                'inventory_item_id' => $item->inventory_item_id,
                                'company_id' => $company->id
                            ]);
                        }

                        if ($item->non_inventory_item_id) {
                            PowderAndInventoryLog::factory()->purchaseorder()->create([
                                'reason_id' => $purchaseOrder->id,
                                'warehouse_id' => $purchaseOrder->warehouse_id,
                                'floor_id' => $purchaseOrder->floor_id,
                                'shelf_id' => $purchaseOrder->shelf_id,
                                'bin_id' => $purchaseOrder->bin_id,
                                'non_inventory_item_id' => $item->non_inventory_item_id,
                                'company_id' => $company->id
                            ]);
                        }
                    }
                }

                Auth::logout();
            }
        }
    }
}
