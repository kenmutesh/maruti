<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['customers'])->get();
        foreach ($companies as $company) {
            if(count($company->customers) > 0){
                Payment::factory()->create([
                    'customer_id' => $company->customers[0]->id,
                    'company_id' => $company->id,
                ]);
                Payment::factory()->nullified()->create([
                    'customer_id' => $company->customers[0]->id,
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}
