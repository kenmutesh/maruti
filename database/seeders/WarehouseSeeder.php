<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['locations'])->get();
        foreach ($companies as $company) {
            foreach ($company->locations as $location) {
                Warehouse::factory(1)->create([
                    'company_id' => $company->id,
                    'location_id' => $location->id
                ]);
            }
        }
    }
}
