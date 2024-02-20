<?php

namespace Database\Seeders;

use App\Models\CashSale;
use App\Models\CoatingJob;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CoatingJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['users', 'customers', 'powders'])->get();
        foreach ($companies as $company) {
            $user = User::where('company_id', $company->id)->first();
            if ($user) {
                Auth::loginUsingId($user->id);
                CoatingJob::factory(2)->cancelledcoatingjob()->create([
                    'powder_id' => $company->powders[0]->id,
                    'customer_id' => $company->customers[0]->id,
                    'prepared_by' => $company->users[0]->id,
                    'supervisor' =>  $company->users[0]->id,
                    'quality_by' =>  $company->users[0]->id,
                    'sale_by' =>  $company->users[0]->id,
                    'created_by' =>  $company->users[0]->id,
                    'company_id' => $company->id
                ]);

                $invoices = Invoice::all();

                foreach ($invoices as $invoice) {
                    if($invoice->cancelled_at){
                        continue;
                    }
                    CoatingJob::factory()->closedcoatingjobinvoice()->create([
                        'invoice_id' => $invoice->id,
                        'powder_id' => $company->powders[0]->id,
                        'customer_id' => $company->customers[0]->id,
                        'prepared_by' => $company->users[0]->id,
                        'supervisor' =>  $company->users[0]->id,
                        'quality_by' =>  $company->users[0]->id,
                        'sale_by' =>  $company->users[0]->id,
                        'created_by' =>  $company->users[0]->id,
                        'company_id' => $company->id
                    ]);
                }

                $cashSales = CashSale::all();

                foreach ($cashSales as $cashSale) {
                    if($cashSale->cancelled_at){
                        continue;
                    }
                    CoatingJob::factory()->closedcoatingjobcashsale()->create([
                        'cash_sale_id' => $cashSale->id,
                        'powder_id' => $company->powders[0]->id,
                        'customer_id' => $company->customers[0]->id,
                        'prepared_by' => $company->users[0]->id,
                        'supervisor' =>  $company->users[0]->id,
                        'quality_by' =>  $company->users[0]->id,
                        'sale_by' =>  $company->users[0]->id,
                        'created_by' =>  $company->users[0]->id,
                        'company_id' => $company->id
                    ]);
                }
                CoatingJob::factory(2)->coatingjobquotation()->create([
                    'powder_id' => $company->powders[0]->id,
                    'customer_id' => $company->customers[0]->id,
                    'prepared_by' => $company->users[0]->id,
                    'supervisor' =>  $company->users[0]->id,
                    'quality_by' =>  $company->users[0]->id,
                    'sale_by' =>  $company->users[0]->id,
                    'created_by' =>  $company->users[0]->id,
                    'company_id' => $company->id
                ]);
                CoatingJob::factory(2)->jobcard()->create([
                    'powder_id' => $company->powders[0]->id,
                    'customer_id' => $company->customers[0]->id,
                    'prepared_by' => $company->users[0]->id,
                    'supervisor' =>  $company->users[0]->id,
                    'quality_by' =>  $company->users[0]->id,
                    'sale_by' =>  $company->users[0]->id,
                    'created_by' =>  $company->users[0]->id,
                    'company_id' => $company->id
                ]);
                Auth::logout();
            }
        }
    }
}
