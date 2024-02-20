<?php

namespace Tests\Feature;

use App\Models\Company;
use Tests\TestCase;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WarehouseFeaturesTest extends TestCase
{
    use RefreshDatabase;

    // TODO:: Smoke test to load document labels belonging to a company

    public function test_can_create_warehouse_for_a_company()
    {
        $location = Location::factory()->create();
        $warehouse = Warehouse::factory()->create([
            'location_id' => $location->id,
            'company_id' => $location->company_id
        ]);
        $this->assertTrue($warehouse->save(),"The warehouse cannot be created");
    }

    public function test_will_load_warehouses_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();
        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            $location = Location::factory()->create([
                'company_id' => $company->id
            ]);
            $location->save();
        }

        $response = $this->actingAs($user)
            ->get('/api/warehouses')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $warehouse) {
                if ($warehouse->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $warehouse->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load warehouses from other companies " . $th->getMessage());
        }
    }
}
