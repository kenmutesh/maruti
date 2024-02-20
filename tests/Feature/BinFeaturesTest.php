<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bin;
use App\Models\Shelf;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BinFeaturesTest extends TestCase
{
    use RefreshDatabase;

    // TODO:: Smoke test to load document labels belonging to a company

    public function test_can_create_bin_for_a_company()
    {
        $shelf = Shelf::factory()->create();
        $bin = Bin::factory()->create([
            'shelf_id' => $shelf->id,
            'company_id' => $shelf->company_id
        ]);
        $this->assertTrue($bin->save(),"The bin cannot be created");
    }

    public function test_will_load_bins_for_only_logged_in_user_company()
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
            $floor = Bin::factory()->create([
                'company_id' => $company->id
            ]);
            $floor->save();
        }

        $response = $this->actingAs($user)
            ->get('/api/bins')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $bin) {
                if ($bin->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $bin->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load bins from other companies " . $th->getMessage());
        }
    }
}
