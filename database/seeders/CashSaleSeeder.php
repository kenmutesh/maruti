<?php

namespace Database\Seeders;

use App\Models\CashSale;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CashSaleSeeder extends Seeder
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
            $user = User::where('company_id', $company->id)->first();
            if($user){
                Auth::loginUsingId($user->id);
                CashSale::factory()->normalcashsale()->create([
                    'customer_id' => $company->customers[0]->id,
                    'company_id' => $company->id
                ]);
                CashSale::factory()->extcashsale()->create([
                    'customer_id' => $company->customers[0]->id,
                    'company_id' => $company->id
                ]);
                Auth::logout();
            }
        }
    }
}
