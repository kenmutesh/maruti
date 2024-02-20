<?php

namespace Tests\Feature;

use App\Enums\DocumentLabelsEnum;
use App\Models\DocumentLabel;
use Tests\TestCase;
use App\Models\Supplier;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class PowderAndInventoryLogsFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_powder_and_inventory_log_for_powder_item_correctly()
    {
        $openingWeight = rand(10, 20);

        $supplier = Supplier::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);

        $documentLabels = DocumentLabelsEnum::cases();
        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $supplier->company_id
            ]);
            $documentLabel->save();
        }

        $user = User::factory()->create([
            'company_id' => $supplier->company_id,
            'role_id' => $role->id
        ]);

        $loggedInUser = Auth::loginUsingId($user->id);

        $powder = Powder::factory()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id,
            'opening_weight' => $openingWeight,
            'current_weight' => $openingWeight
        ]);

        $powder->save();

        $purchaseOrder = PurchaseOrder::factory()->closedpurchaseorder()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id,
        ]);
        
        $purchaseOrderItem = PurchaseOrderItem::factory()->powder()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'powder_id' => $powder->id,
        ]);

        $powderAndInventoryLog = PowderAndInventoryLog::factory()->purchaseorderpowder()->create([
            'sum_added' => $purchaseOrderItem->quantity,
            'reason_id' => $purchaseOrder->id,
            'warehouse_id' => $purchaseOrder->warehouse_id,
            'floor_id' => $purchaseOrder->floor_id,
            'shelf_id' => $purchaseOrder->shelf_id,
            'bin_id' => $purchaseOrder->bin_id,
            'powder_id' => $purchaseOrderItem->powder_id
        ]);

        $powderAndInventoryLog->save();

        $powder = $powder->fresh();

        $this->actingAs($loggedInUser)->assertTrue(($powder->current_weight == $powder->opening_weight + $powderAndInventoryLog->sum_added),"The tally method by opening weight and inventory log is not ok current weight is: ". $powder->current_weight ." log is: ". $powderAndInventoryLog->sum_added ." and the opening weight is: ". $powder->opening_weight );

        $this->actingAs($loggedInUser)->assertTrue(($powder->current_weight == $powder->opening_weight + $purchaseOrderItem->quantity),"The tally by opening weight and item quantity is not ok. Current weight is: ". $powder->current_weight ." item quantity is: ". $purchaseOrderItem->quantity ." and the opening weight is: ". $powder->opening_weight );
    }
    
}
