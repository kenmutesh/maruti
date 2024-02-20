<?php

namespace Tests\Feature;

use App\Enums\DocumentLabelsEnum;
use Tests\TestCase;
use App\Models\Company;
use App\Models\DocumentLabel;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDocument;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_will_load_purchase_orders_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $companies[0]->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($companies as $company) {
            foreach ($documentLabels as $document) {
                $documentLabel = DocumentLabel::factory()->create([
                    'document' => $document->value,
                    'company_id' => $company->id
                ]);
                $documentLabel->save();
            }

            $purchaseOrder = PurchaseOrder::factory(3)->create([
                'company_id' => $company->id,
                'supplier_id' => $supplier->id
            ]);
        }

        $response = $this->actingAs($loggedInUser)
            ->get('/api/purchaseorders')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $purchaseOrder) {
                if ($purchaseOrder->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $purchaseOrder->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load purchase orders from other companies " . $th->getMessage());
        }
    }

    public function test_will_follow_purchase_order_document_label_document_prefix_sequence()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrderOne = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrderOne->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::PURCHASEORDER->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $documentLabelEdit = DocumentLabel::find($documentLabel->id);

        $documentLabelEdit->document_prefix = 'XYZ';

        $documentLabelEdit->update();

        $purchaseOrderTwo = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrderTwo->save();

        $this->assertTrue(($purchaseOrderOne->lpo_prefix === $documentLabel->document_prefix), "Expected " . $documentLabel->document_prefix . " got " . $purchaseOrderOne->lpo_prefix);

        $this->assertTrue(($purchaseOrderTwo->lpo_prefix === $documentLabelEdit->document_prefix), "Expected " . $documentLabelEdit->document_prefix . " got " . $purchaseOrderTwo->lpo_prefix);
    }

    public function test_will_follow_purchase_order_document_label_document_suffix_sequence()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrderOne = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrderOne->save();

        $purchaseOrderTwo = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrderTwo->save();

        $documentLabel = DocumentLabel::where([
            'document' => DocumentLabelsEnum::PURCHASEORDER->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $this->assertTrue(($purchaseOrderOne->lpo_suffix === ($documentLabel->document_suffix + 1)), "Expected " . ($documentLabel->document_suffix + 1) . " got " . $purchaseOrderOne->lpo_suffix);

        $this->assertTrue(($purchaseOrderTwo->lpo_suffix === ($documentLabel->document_suffix + 2)), "Expected " . ($documentLabel->document_suffix + 2) . " got " . $purchaseOrderOne->lpo_suffix);
    }

    public function test_will_load_purchase_order_with_associated_documents()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrder = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrder->save();

        PurchaseOrderDocument::factory(3)->create([
            'purchase_order_id' => $purchaseOrder->id
        ]);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/purchaseorders/' . $purchaseOrder->id)
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);

        $this->assertTrue(count($data->purchaseorderdocuments) === 3);
    }

    public function test_will_load_purchase_order_with_associated_items()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrder = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrder->save();

        $powderCount = count(PurchaseOrderItem::factory(rand(1, 5))->powder()->create([
            'purchase_order_id' => $purchaseOrder->id
        ]));

        $inventoryItemCount = count(PurchaseOrderItem::factory(rand(1, 5))->inventoryitem()->create([
            'purchase_order_id' => $purchaseOrder->id
        ]));

        $nonInventoryItemCount = count(PurchaseOrderItem::factory(rand(1, 5))->noninventoryitem()->create([
            'purchase_order_id' => $purchaseOrder->id
        ]));

        $response = $this->actingAs($loggedInUser)
            ->get('/api/purchaseorders/' . $purchaseOrder->id)
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);

        $this->assertTrue(count($data->purchaseorderitems) === ($powderCount + $inventoryItemCount + $nonInventoryItemCount));
    }

    public function test_will_load_purchase_order_with_correct_sub_total()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrder = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrder->save();

        $powderItems = PurchaseOrderItem::factory(2)->powder()->create([
            'purchase_order_id' => $purchaseOrder->id
        ]);

        $powderTotal = array_reduce($powderItems->toArray(), function($accumulator, $item){
            return $accumulator + $item['cost'];
        }, 0);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/purchaseorders/' . $purchaseOrder->id)
            ->decodeResponseJson()
            ->json;

        $fetchedPurchaseOrder = json_decode($response);

        $this->assertTrue($fetchedPurchaseOrder->sub_total == $powderTotal, "Expected ". $powderTotal ." got ".$fetchedPurchaseOrder->sub_total );
    }

    public function test_will_load_purchase_order_with_correct_vat_total()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrder = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrder->save();

        $powderItems = PurchaseOrderItem::factory(2)->powder()->create([
            'purchase_order_id' => $purchaseOrder->id
        ]);

        $powderVatTotal = array_reduce($powderItems->toArray(), function($accumulator, $item){
            return $accumulator + $item['vat'];
        }, 0);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/purchaseorders/' . $purchaseOrder->id)
            ->decodeResponseJson()
            ->json;

        $fetchedPurchaseOrder = json_decode($response);

        $this->assertTrue($fetchedPurchaseOrder->vat == $powderVatTotal, "Expected ". $powderVatTotal ." got ".$fetchedPurchaseOrder->sub_total );
    }

    public function test_will_load_purchase_order_with_correct_grand_total()
    {
        $company = Company::factory()->create();

        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $loggedInUser = Auth::loginUsingId($user->id);
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id,
        ]);
        $documentLabels = DocumentLabelsEnum::cases();

        foreach ($documentLabels as $document) {
            $documentLabel = DocumentLabel::factory()->create([
                'document' => $document->value,
                'company_id' => $company->id
            ]);
            $documentLabel->save();
        }

        $purchaseOrder = PurchaseOrder::factory()->create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id
        ]);

        $purchaseOrder->save();

        $powderItems = PurchaseOrderItem::factory(2)->powder()->create([
            'purchase_order_id' => $purchaseOrder->id
        ]);

        $powderSubTotal = array_reduce($powderItems->toArray(), function($accumulator, $item){
            return $accumulator + $item['sub_total'];
        }, 0);

        $response = $this->actingAs($loggedInUser)
            ->get('/api/purchaseorders/' . $purchaseOrder->id)
            ->decodeResponseJson()
            ->json;

        $fetchedPurchaseOrder = json_decode($response);

        $this->assertTrue(($fetchedPurchaseOrder->grand_total - $purchaseOrder->discount) == $powderSubTotal, "Expected ". $powderSubTotal ." got ".$fetchedPurchaseOrder->grand_total );
    }
}
