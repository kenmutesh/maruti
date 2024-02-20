<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['warehouses'])->get();
        foreach ($companies as $company) {
            foreach ($company->warehouses as $warehouse) {
                Floor::factory()->create([
                    'company_id' => $company->id,
                    'warehouse_id' => $warehouse->id
                ]);
            }
        }
    }
}
