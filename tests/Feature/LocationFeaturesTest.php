<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_location_for_a_company()
    {
        $company = Company::factory()->create();
        $location = Location::factory()->create([
            'company_id' => $company->id
        ]);
        $this->assertTrue($location->save(),"The location cannot be created");
    }

    public function test_will_load_locations_for_only_logged_in_user_company()
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
            ->get('/api/locations')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $location) {
                if ($location->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $location->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load locations from other companies " . $th->getMessage());
        }
    }

}
