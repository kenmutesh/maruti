<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $user = User::where('company_id', $company->id)->first();
            if($user){
                Auth::loginUsingId($user->id);

                $supplier = Supplier::orderBy('id', 'desc')->first();
    
                PurchaseOrder::factory()->create([
                    'company_id' => $company->id,
                    'supplier_id' => $supplier->id
                ]);

                PurchaseOrder::factory()->cancelledpurchaseorder()->create([
                    'company_id' => $company->id,
                    'supplier_id' => $supplier->id
                ]);

                $bin = Bin::orderBy('id','desc')->with(['shelf.floor.warehouse'])->first();

                PurchaseOrder::factory()->closedpurchaseorder()->create([
                    'company_id' => $company->id,
                    'supplier_id' => $supplier->id,
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id
                ]);

                Auth::logout();
            }
        }
    }
}
