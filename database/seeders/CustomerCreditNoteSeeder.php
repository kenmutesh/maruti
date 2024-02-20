<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CustomerCreditNote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CustomerCreditNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['invoices', 'customers'])->get();
        foreach ($companies as $company) {
            $user = User::where('company_id', $company->id)->first();
            if ($user) {
                Auth::loginUsingId($user->id);
                if (count($company->payments) > 0 && count($company->invoices) > 0) {
                    CustomerCreditNote::factory(2)->create([
                        'customer_id' => $company->customers[0]->id,
                        'invoice_id' => $company->invoices[0]->id,
                        'company_id' => $company->id
                    ]);
                }
                Auth::logout();
            }
        }
    }
}
