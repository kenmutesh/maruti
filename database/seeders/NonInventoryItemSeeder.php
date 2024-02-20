<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\NonInventoryItem;
use Illuminate\Database\Seeder;

class NonInventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['suppliers'])->get();
        foreach ($companies as $company) {
            NonInventoryItem::factory(2)->create([
                'company_id' => $company->id,
                'supplier_id' => $company->suppliers[0]->id
            ]);
        }
    }
}
