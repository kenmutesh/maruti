<?php

namespace Database\Seeders;

use App\Models\AprotecUser;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aprotecUsers = AprotecUser::all();
        foreach ($aprotecUsers as $aprotecUser) {
            Company::factory()->create([
                'created_by' => $aprotecUser->id
            ]);
        }
    }
}
