<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerCreditNote;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerCreditNoteFactory extends Factory
{
    public function definition()
    {
        $customerCreditNote = new CustomerCreditNote();

        return [
            'credit_note_prefix' => $customerCreditNote->next_credit_note_prefix,
            'credit_note_suffix' => $customerCreditNote->next_credit_note_suffix,
            'customer_id' => Customer::factory(),
            'invoice_id' => Invoice::factory(),
            'record_date' => $this->faker->dateTimeBetween('+1 days', '+5 days'),            
            'company_id' => Company::factory(),
        ];
    }

}
