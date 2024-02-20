<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RoleFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    // TODO:: Smoke test to load document labels belonging to a company

    public function test_can_create_roles_for_a_company()
    {
        $company = Company::factory()->create();
        $role = Role::factory()->create([
            'company_id' => $company->id
        ]);
        $this->assertTrue($role->save(),"The roles can be created");
    }
}
