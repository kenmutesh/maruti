<?php

namespace Database\Seeders;

use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\Company;
use App\Models\InventoryItem;
use App\Models\PowderAndInventoryLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::with(['suppliers'])->get();
        foreach ($companies as $company) {
            $aluminium = InventoryItem::factory()->aluminium()->create([
                'company_id' => $company->id,
                'supplier_id' => $company->suppliers[0]->id
            ]);
            $hardware = InventoryItem::factory()->hardware()->create([
                'company_id' => $company->id,
                'supplier_id' => $company->suppliers[0]->id
            ]);

            $user = User::where('company_id', $company->id)->first();
            
            Auth::loginUsingId($user->id);

            $bin = Bin::orderBy('id', 'desc')->first();

            $inventoryLog = new PowderAndInventoryLog();

            $inventoryLog->fill([
                'reason' => PowderAndInventoryLogsEnum::CREATING,
                'reason_id' => $aluminium->id,
                'sum_added' => $aluminium->current_quantity,
                'inventory_item_id' => $aluminium->id,
                'warehouse_id' => $bin->shelf->floor->warehouse_id,
                'floor_id' => $bin->shelf->floor_id,
                'shelf_id' => $bin->shelf_id,
                'bin_id' => $bin->id,
                'company_id' => auth()->user()->company_id
            ]);

            $inventoryLog->saveQuietly();

            $inventoryLog = new PowderAndInventoryLog();

            $inventoryLog->fill([
                'reason' => PowderAndInventoryLogsEnum::CREATING,
                'reason_id' => $hardware->id,
                'sum_added' => $hardware->current_quantity,
                'inventory_item_id' => $hardware->id,
                'warehouse_id' => $bin->shelf->floor->warehouse_id,
                'floor_id' => $bin->shelf->floor_id,
                'shelf_id' => $bin->shelf_id,
                'bin_id' => $bin->id,
                'company_id' => auth()->user()->company_id
            ]);

            $inventoryLog->saveQuietly();
            Auth::logout();
        }
    }
}
