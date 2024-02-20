<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerCCEmail;
use Illuminate\Database\Seeder;

class CustomerCCEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = Customer::all();
        foreach ($customers as $customer) {
            CustomerCCEmail::factory(2)->create([
                'customer_id' => $customer->id
            ]);
        }
    }
}
