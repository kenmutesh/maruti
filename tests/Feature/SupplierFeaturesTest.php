<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class SupplierFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_can_create_suppliers_for_a_company()
    {
        $company = Company::factory()->create();
        $supplier = Supplier::factory()->create([
            'company_id' => $company->id
        ]);
        $this->assertTrue($supplier->save(),"The supplier can be created");
    }

    public function test_will_load_suppliers_for_only_logged_in_user_company()
    {
        $companies = Company::factory(3)->create();
        $role = Role::factory()->create([
            'name' => 'ADMIN',
            'company_id' => $companies[0]->id,
        ]);
        $role->save();
        $user = User::factory()->create([
            'company_id' => $companies[0]->id,
            'role_id' => $role->id
        ]);
        $user->save();
        foreach ($companies as $company) {
            $supplier = Supplier::factory()->create([
                'company_id' => $company->id
            ]);
            $supplier->save();
        }

        $authUser = Auth::loginUsingId($user->id);

        $response = $this->actingAs($authUser)
            ->get('/api/suppliers')
            ->decodeResponseJson()
            ->json;

        $data = json_decode($response);
        try {
            foreach ($data as $supplier) {
                if ($supplier->company_id != $user->company_id) {
                    throw new \Exception("Found data with wrong company id, expected " . $user->company_id . " found " . $supplier->company_id, 1);
                }
            }
            $this->assertTrue(true);
        } catch (\Throwable $th) {
            $this->fail("Can load document labels from other companies " . $th->getMessage());
        }
    }

    public function test_will_load_supplier_for_logged_in_user_with_view_supplier_privilege()
    {
        $company = Company::factory()->create();

        $supplierPrivileges = array(
            'view' => true
        );

        $privileges = array(
            'supplier' => $supplierPrivileges
        );

        $role = Role::factory()->create([
            'name' => 'ADMIN',
            'privileges' => json_encode($privileges),
            'company_id' => $company->id,
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);

        Supplier::factory(2)->create([
            'company_id' => $company->id
        ]);

        $authUser = Auth::loginUsingId($user->id);

        $response = $this->actingAs($authUser)
            ->get('/api/suppliers');
        
        $response->assertStatus(200);
    }

    public function test_will_not_load_supplier_for_logged_in_user_without_view_supplier_privilege()
    {
        $company = Company::factory()->create();

        $supplierPrivileges = array(
            'view' => false
        );

        $privileges = array(
            'supplier' => $supplierPrivileges
        );

        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'privileges' => json_encode($privileges),
            'company_id' => $company->id,
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);

        $authUser = Auth::loginUsingId($user->id);

        Supplier::factory(2)->create([
            'company_id' => $company->id
        ]);        

        $response = $this->actingAs($authUser)
            ->get('/api/suppliers');
        
        $response->assertStatus(403);
    }

    public function test_will_not_create_supplier_for_logged_in_user_without_create_supplier_privilege()
    {
        $company = Company::factory()->create();

        $supplierPrivileges = array(
            'create' => false
        );

        $privileges = array(
            'supplier' => $supplierPrivileges
        );

        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'privileges' => json_encode($privileges),
            'company_id' => $company->id,
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);

        Supplier::factory(2)->create([
            'company_id' => $company->id
        ]);  
        
        $authUser = Auth::loginUsingId($user->id);

        $response = $this->actingAs($authUser)
            ->post('/api/suppliers', [
                'supplier_name' => 'xlvisben',
                'supplier_email' => 'xlvisben@xlvisben.com',
                'supplier_mobile' => '012345678',
                'company_location' => 'Nairobi',
                'company_pin' => '12345678',
                'company_box' => '12345'
            ]);
        
        $response->assertStatus(403);
    }
    
    public function test_will_not_update_supplier_for_logged_in_user_without_update_supplier_privilege()
    {
        $company = Company::factory()->create();

        $supplierPrivileges = array(
            'update' => false
        );

        $privileges = array(
            'supplier' => $supplierPrivileges
        );

        $role = Role::factory()->create([
            'name' => 'OTHER ROLE',
            'privileges' => json_encode($privileges),
            'company_id' => $company->id
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id
        ]);

        Supplier::factory(2)->create([
            'company_id' => $company->id
        ]);    
        
        $supplier = Supplier::all()->first();

        $authUser = Auth::loginUsingId($user->id);

        $response = $this->actingAs($authUser)
            ->put('/api/suppliers/'.$supplier->id, [
                'supplier_name' => 'xlvisben',
                'supplier_email' => 'xlvisben@xlvisben.com',
                'supplier_mobile' => '012345678',
                'company_location' => 'Nairobi',
                'company_pin' => '12345678',
                'company_box' => '12345'
            ]);
        
        $response->assertStatus(403);
    }

}
