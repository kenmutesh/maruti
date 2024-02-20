<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use App\Models\NonInventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class NonInventoryItemFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_can_create_non_inventory_item_with_supplier_for_a_company()
    {
        $supplier = Supplier::factory()->create();
        $nonInventoryItem = NonInventoryItem::factory()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id
        ]);
        $this->assertTrue($nonInventoryItem->save(),"The non inventory item cannot be created");
    }

    public function test_will_load_non_inventory_items_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();
        $role = Role::factory()->create([
            'name' => 'ADMIN',
            'company_id' => $companies[0]->id
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            NonInventoryItem::factory(5)->create([
                'company_id' => $companies[0]->id,
            ]);
        }

        $authUser = Auth::loginUsingId($user->id);

        $response = $this->actingAs($authUser)
            ->get('/api/noninventoryitems')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $nonInventoryItem) {
                if ($nonInventoryItem->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $nonInventoryItem->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load document labels from other companies " . $th->getMessage());
        }
    }
}
