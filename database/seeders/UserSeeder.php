<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['userroles'])->get();
        foreach ($companies as $company) {
            foreach ($company->userroles as $role) {
                User::factory(2)->create([
                    'company_id' => $company->id,
                    'role_id' => $role->id
                ]);
                User::factory(2)->verified()->create([
                    'company_id' => $company->id,
                    'role_id' => $role->id
                ]);
            }
        }
    }
}
