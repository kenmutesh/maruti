<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        foreach($companies as $company){
            Tax::factory()->create([
                'company_id' => $company->id,
            ]);
        }
    }
}
