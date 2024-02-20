<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class InventoryItemFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_can_create_aluminium_inventory_item_with_supplier_for_a_company()
    {
        $supplier = Supplier::factory()->create();
        $inventoryItem = InventoryItem::factory()->aluminium()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id
        ]);
        $this->assertTrue($inventoryItem->save(),"The aluminium inventory item cannot be created");
    }

    public function test_can_create_hardware_inventory_item_with_supplier_for_a_company()
    {
        $supplier = Supplier::factory()->create();
        $inventoryItem = InventoryItem::factory()->hardware()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id
        ]);
        $this->assertTrue($inventoryItem->save(),"The aluminium inventory item cannot be created");
    }

    public function test_can_create_inventory_item_with_correct_taxed_price()
    {
        $supplier = Supplier::factory()->create();
        $inventoryItem = InventoryItem::factory()->hardware()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id,
            'price' => 1,
            'vat' => 16
        ]);
        $inventoryItem->save();
        
        $this->assertTrue(($inventoryItem->taxed_price == 1.16),"The inventory item doesn't have the correct taxed price");
    }

    public function test_will_load_inventory_items_for_logged_in_user_with_view_inventory_items_privilege()
    {
        $company = Company::factory()->create();

        $inventoryItemsPrivileges = array(
            'view' => true
        );

        $privileges = array(
            'inventoryitem' => $inventoryItemsPrivileges
        );

        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'privileges' => json_encode($privileges)
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);

        Supplier::factory(2)->create([
            'company_id' => $company->id
        ]);

        $response = $this->actingAs($user)
            ->get('/api/inventoryitems');
        
        $response->assertStatus(200);
    }
}
