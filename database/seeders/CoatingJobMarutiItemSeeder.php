<?php

namespace Database\Seeders;

use App\Models\CoatingJobMarutiItem;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CoatingJobMarutiItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['coatingjobs', 'inventoryitems', 'warehouses', 'floors', 'shelves', 'bins'])->get();

        foreach ($companies as $company) {
            $user = User::where('company_id', $company->id)->first();
            if ($user) {
                Auth::loginUsingId($user->id);
                if (count($company->coatingjobs) > 0 && count($company->inventoryitems) > 0 && count($company->warehouses) > 0 && count($company->floors) > 0 && count($company->shelves) > 0 && count($company->bins) > 0) {
                    CoatingJobMarutiItem::factory()->create([
                        'coating_job_id' => $company->coatingjobs[0]->id,
                        'inventory_item_id' => $company->inventoryitems[0]->id,
                        'warehouse_id' => $company->warehouses[0]->id,
                        'floor_id' => $company->floors[0]->id,
                        'shelf_id' => $company->shelves[0]->id,
                        'bin_id' => $company->bins[0]->id,
                    ]);

                    CoatingJobMarutiItem::factory()->customitem()->create([
                        'coating_job_id' => $company->coatingjobs[0]->id,
                        'warehouse_id' => null,
                        'floor_id' => null,
                        'shelf_id' => null,
                        'bin_id' => null
                    ]);
                }

                Auth::logout();
            }
        }
    }
}
