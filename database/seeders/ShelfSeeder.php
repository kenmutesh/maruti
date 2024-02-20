<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class ShelfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['floors'])->get();
        foreach ($companies as $company) {
            foreach ($company->floors as $floor) {
                Shelf::factory()->create([
                    'company_id' => $company->id,
                    'floor_id' => $floor->id
                ]);
            }
        }
    }
}
