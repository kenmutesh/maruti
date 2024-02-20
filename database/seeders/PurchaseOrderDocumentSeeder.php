<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDocument;
use Illuminate\Database\Seeder;

class PurchaseOrderDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purchaseOrders = PurchaseOrder::all();
        foreach ($purchaseOrders as $purchaseOrder) {
            PurchaseOrderDocument::factory()->memo()->create([
                'purchase_order_id' => $purchaseOrder->id
            ]);
            PurchaseOrderDocument::factory()->invoice()->create([
                'purchase_order_id' => $purchaseOrder->id
            ]);
            PurchaseOrderDocument::factory()->delivery()->create([
                'purchase_order_id' => $purchaseOrder->id
            ]);
        }
    }
}
