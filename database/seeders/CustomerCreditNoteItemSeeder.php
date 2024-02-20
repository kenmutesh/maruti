<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CustomerCreditNoteItem;
use Illuminate\Database\Seeder;

class CustomerCreditNoteItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['customercreditnotes', 'powders', 'inventoryitems'])->get();
        foreach ($companies as $company) {
            if (count($company->customercreditnotes) > 0 && count($company->powders) > 0 && count($company->inventoryitems) > 0) {
            CustomerCreditNoteItem::factory()->powder()->create([
                'customer_credit_note_id' => $company->customercreditnotes[0]->id,
                'powder_id' => $company->powders[0]->id
            ]);
            CustomerCreditNoteItem::factory()->inventoryitem()->create([
                'customer_credit_note_id' => $company->customercreditnotes[0]->id,
                'inventory_item_id' => $company->inventoryitems[0]->id
            ]);
            CustomerCreditNoteItem::factory()->customitem()->create([
                'customer_credit_note_id' => $company->customercreditnotes[0]->id,
            ]);
        }
        }
    }
}
