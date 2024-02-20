<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            Role::factory()->admin()->create([
                'company_id' => $company->id
            ]);
            $privileges['coatingjob']['view'] = true;
            Role::factory()->create([
                'company_id' => $company->id,
                'privileges' => json_encode($privileges)
            ]);
        }
    }
}
