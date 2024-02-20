<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class UserFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_can_create_user_for_a_company()
    {
        $company = Company::factory()->create();
        $role = Role::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $this->assertTrue($user->save(),"The users for a company can be created");
    }

    public function test_can_view_users_as_admin_relevant_to_company(){
        $companies = Company::factory(3)->create();
        $otherRoles = Role::factory(3)->create();
        $roleAdmin = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $userAdmin = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $roleAdmin->id
        ]);
        foreach ($companies as $company) {
            foreach ($otherRoles as $role) {
                $user = User::factory()->create([
                    'role_id' => $role->id,
                    'company_id' => $company->id
                ]);
                $user->save();
            }
        }

        $response = $this->actingAs($userAdmin)
            ->get('/api/users')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $user) {
                if ($user->company_id != $userAdmin->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $userAdmin->company_id . " found " . $user->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load users from other companies " . $th->getMessage());
        }
    }

    public function test_can_add_users_as_admin_relevant_to_company(){
        $company = Company::factory()->create();
        $roleAdmin = Role::factory()->create([
            'name' => 'ADMIN',
        ]);
        $otherRole = Role::factory()->create();
        $userAdmin = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $roleAdmin->id
        ]);

        $response = $this->actingAs($userAdmin)
            ->post('/api/users',[
                'username' => 'xlvisben',
                'email' => 'xlvisben@elvisben.com',
                'password' => 12345678,
                'company_id' => $company->id,
                'role_id' => $otherRole->id
            ]);
        $response->assertStatus(201);
    }

    public function test_cannot_add_users_as_other_role_relevant_to_company(){
        $company = Company::factory()->create();
        $roleAdmin = Role::factory()->create([
            'name' => 'OTHER ROLE',
        ]);
        $otherRole = Role::factory()->create();
        $userAdmin = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $roleAdmin->id
        ]);

        $response = $this->actingAs($userAdmin)
            ->post('/api/users',[
                'username' => 'xlvisben',
                'email' => 'xlvisben@elvisben.com',
                'password' => 12345678,
                'company_id' => $company->id,
                'role_id' => $otherRole->id
            ]);
        $response->assertStatus(403);
    }

    public function test_will_not_load_users_if_not_an_admin()
    {
        $companies = Company::factory(1)->create();
        $otherRoles = Role::factory(3)->create();
        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
        ]);
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        foreach ($companies as $company) {
            foreach ($otherRoles as $role) {
                $user = User::factory()->create([
                    'role_id' => $role->id,
                    'company_id' => $company->id
                ]);
                $user->save();
            }
        }

        $response = $this->actingAs($user)
            ->get('/api/users');
        $response->assertStatus(403);
    }

    public function test_will_login_valid_user()
    {
        $company = Company::factory()->create();
        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
        ]);
        $user = User::factory()->create([
            'username' => 'xlvisben',
            'email' => 'xlvisben@xlvisben.me.ke',
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);
        $user->save();
        $this->assertTrue((Auth::attempt(['email' => $user->email, 'password' => '12345678']) ||
        Auth::attempt(['username' => $user->username, 'password' => '12345678'])));
    }

}
