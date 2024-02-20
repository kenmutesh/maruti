<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['customers', 'closedcoatingjobs'])->get();
        foreach ($companies as $company) {
            $user = User::where('company_id', $company->id)->first();
            if($user){
                Auth::loginUsingId($user->id);

                Invoice::factory()->overninetydayperiod()->create([
                    'customer_id' => $company->customers[0]->id,
                    'created_by' => $user->id,
                    'company_id' => $company->id
                ]);

                Invoice::factory()->ninetydayperiod()->create([
                    'customer_id' => $company->customers[0]->id,
                    'created_by' => $user->id,
                    'company_id' => $company->id
                ]);

                Invoice::factory()->sixtydayperiod()->create([
                    'customer_id' => $company->customers[0]->id,
                    'created_by' => $user->id,
                    'company_id' => $company->id
                ]);

                Invoice::factory()->thirtydayperiod()->create([
                    'customer_id' => $company->customers[0]->id,
                    'created_by' => $user->id,
                    'company_id' => $company->id
                ]);

                Invoice::factory()->create([
                    'customer_id' => $company->customers[0]->id,
                    'created_by' => $user->id,
                    'company_id' => $company->id
                ]);
                Invoice::factory()->cancelled()->create([
                    'customer_id' => $company->customers[0]->id,
                    'created_by' => $user->id,
                    'company_id' => $company->id
                ]);
                                
                Auth::logout();
            }
        }
        
    }
}
