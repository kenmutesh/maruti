<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['shelves'])->get();
        foreach ($companies as $company) {
            foreach ($company->shelves as $shelf) {
                Bin::factory()->create([
                    'company_id' => $company->id,
                    'shelf_id' => $shelf->id
                ]);
            }
        }
    }
}
