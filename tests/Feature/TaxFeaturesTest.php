<?php

namespace Tests\Feature;

use App\Enums\TaxTypesEnum;
use Tests\TestCase;
use App\Models\Company;
use App\Models\Role;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaxFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    // TODO:: Smoke test to load tax belonging to a company

    public function test_can_create_tax_type_for_a_company()
    {
        $company = Company::factory()->create();
        $taxes = TaxTypesEnum::cases();
        // associate all companies with tax
        try {
            foreach ($taxes as $tax) {
                $tax = Tax::factory()->create([
                    'percentage' => 16,
                    'type' => $tax->value,
                    'company_id' => $company->id
                ]);
                $tax->save();
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("The company and associated document labels cannot be created. ". $th->getMessage());
        }
    }

    public function test_will_load_taxes_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();
        $taxes = TaxTypesEnum::cases();
        $role = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            foreach ($taxes as $tax) {
                $tax = Tax::factory()->create([
                    'type' => $tax->value,
                    'company_id' => $company->id
                ]);
                $tax->save();
            }
        }

        $response = $this->actingAs($user)
            ->get('/api/taxes')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $tax) {
                if ($tax->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $tax->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load taxes from other companies " . $th->getMessage());
        }
    }

    public function test_will_not_load_taxes_if_not_an_admin()
    {
        $companies = Company::factory(1)->create();
        $taxes = TaxTypesEnum::cases();
        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            foreach ($taxes as $tax) {
                $tax = Tax::factory()->create([
                    'type' => $tax->value,
                    'company_id' => $company->id
                ]);
                $tax->save();
            }
        }

        $response = $this->actingAs($user)
            ->get('/api/taxes');
        $response->assertStatus(403);
    }

    public function test_admin_role_can_edit_tax()
    {
        $companies = Company::factory(1)->create();
        $taxes = TaxTypesEnum::cases();
        $role = Role::factory()->admin()->create();
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);

        foreach ($companies as $company) {
            foreach ($taxes as $tax) {
                $tax = Tax::factory()->create([
                    'type' => $tax->value,
                    'company_id' => $company->id
                ]);
                $tax->save();
            }
        }

        $taxEdit = Tax::get()->first();

        $response = $this->actingAs($user)
            ->put('/api/taxes/update', 
                [
                    'id' => $taxEdit->id,
                    'percentage' => 10,
                ]);
        $response->assertStatus(200);
    }

    public function test_other_roles_cannot_edit_document_label()
    {
        $companies = Company::factory(1)->create();
        $taxes = TaxTypesEnum::cases();
        $role = Role::factory()->create([
            'name' => 'OTHER ROLE'
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);

        foreach ($companies as $company) {
            foreach ($taxes as $tax) {
                $tax = Tax::factory()->create([
                    'type' => $tax->value,
                    'company_id' => $company->id
                ]);
                $tax->save();
            }
        }

        $taxEdit = Tax::get()->first();

        $response = $this->actingAs($user)
            ->put('/api/taxes/update', 
                [
                    'id' => $taxEdit->id,
                    'percentage' => 10,
                ]);
        $response->assertStatus(403);
    }
}
