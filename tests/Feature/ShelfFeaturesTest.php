<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Floor;
use App\Models\Shelf;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShelfFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_shelf_for_a_company()
    {
        $floor = Floor::factory()->create();
        $shelf = Shelf::factory()->create([
            'floor_id' => $floor->id,
            'company_id' => $floor->company_id
        ]);
        $this->assertTrue($shelf->save(),"The shelf cannot be created");
    }

    public function test_will_load_shelves_for_only_logged_in_user_company()
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
            $floor = Shelf::factory()->create([
                'company_id' => $company->id
            ]);
            $floor->save();
        }

        $response = $this->actingAs($user)
            ->get('/api/shelves')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $shelf) {
                if ($shelf->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $shelf->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load shelves from other companies " . $th->getMessage());
        }
    }
}
