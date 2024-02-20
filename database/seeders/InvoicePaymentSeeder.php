<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\InvoicePayment;
use Illuminate\Database\Seeder;

class InvoicePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['payments', 'invoices'])->get();
        foreach ($companies as $company) {
            if (count($company->payments) > 0 && count($company->invoices) > 0) {
                InvoicePayment::factory()->openingbalancepayment()->create([
                    'payment_id' => $company->payments[0]->id,
                ]);
                InvoicePayment::factory(2)->create([
                    'payment_id' => $company->payments[0]->id,
                    'invoice_id' => $company->invoices[0]->id,
                ]);

                InvoicePayment::factory(2)->create([
                    'payment_id' => $company->payments[1]->id,
                    'invoice_id' => $company->invoices[1]->id,
                ]);
            }
        }
    }
}
