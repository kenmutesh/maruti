<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\Powder;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class PowderFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_can_create_powder_with_supplier_for_a_company()
    {
        $supplier = Supplier::factory()->create();
        $powder = Powder::factory()->create([
            'supplier_id' => $supplier->id,
            'company_id' => $supplier->company_id
        ]);
        $this->assertTrue($powder->save(),"The powder cannot be created");
    }

    public function test_will_load_powder_for_logged_in_user_with_view_powder_privilege()
    {
        $company = Company::factory()->create();

        $powderPrivileges = array(
            'view' => true
        );

        $privileges = array(
            'powder' => $powderPrivileges
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

        $loggedInUser = Auth::loginUsingId($user->id);

        Supplier::factory(2)->create([
            'company_id' => $company->id
        ]);

        $authUser = Auth::loginUsingId($user->id);

        $response = $this->actingAs($authUser)
            ->get('/api/powders');
        
        $response->assertStatus(200);
    }
    
}
