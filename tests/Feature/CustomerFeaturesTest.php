<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_customers_for_a_company()
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create([
            'company_id' => $company->id
        ]);
        $this->assertTrue($customer->save(),"The customer can be created");
    }

    public function test_will_load_customers_for_only_logged_in_user_company()
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
            $customer = Customer::factory()->create([
                'company_id' => $company->id
            ]);
            $customer->save();
        }

        $response = $this->actingAs($user)
            ->get('/api/customers')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $customer) {
                if ($customer->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $customer->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load customers from other companies " . $th->getMessage());
        }
    }
}
