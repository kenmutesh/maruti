<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Floor;
use App\Models\Warehouse;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FloorFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_floor_for_a_company()
    {
        $warehouse = Warehouse::factory()->create();
        $floor = Floor::factory()->create([
            'warehouse_id' => $warehouse->id,
            'company_id' => $warehouse->company_id
        ]);
        $this->assertTrue($floor->save(),"The floor cannot be created");
    }

    public function test_will_load_floors_for_only_logged_in_user_company()
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
            $floor = Floor::factory()->create([
                'company_id' => $company->id
            ]);
            $floor->save();
        }

        $response = $this->actingAs($user)
            ->get('/api/floors')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $floor) {
                if ($floor->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $floor->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load floors from other companies " . $th->getMessage());
        }
    }
}
