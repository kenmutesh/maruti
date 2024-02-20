<?php

namespace Database\Seeders;

use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\Company;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class PowderSeeder extends Seeder
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
            $powders = Powder::factory(2)->create([
                'company_id' => $company->id,
                'supplier_id' => $company->supplierS[0]->id
            ]);

            $user = User::where('company_id', $company->id)->first();

            Auth::loginUsingId($user->id);

            $bin = Bin::orderBy('id', 'desc')->first();

            $powderLog = new PowderAndInventoryLog();

            $powderLog->fill([
                'reason' => PowderAndInventoryLogsEnum::CREATING,
                'reason_id' => $powders[0]->id,
                'sum_added' => $powders[0]->current_weight,
                'powder_id' => $powders[0]->id,
                'warehouse_id' => $bin->shelf->floor->warehouse_id,
                'floor_id' => $bin->shelf->floor_id,
                'shelf_id' => $bin->shelf_id,
                'bin_id' => $bin->id,
                'company_id' => auth()->user()->company_id
            ]);

            $powderLog->saveQuietly();

            $powderLog = new PowderAndInventoryLog();

            $powderLog->fill([
                'reason' => PowderAndInventoryLogsEnum::CREATING,
                'reason_id' => $powders[1]->id,
                'sum_added' => $powders[1]->current_weight,
                'powder_id' => $powders[1]->id,
                'warehouse_id' => $bin->shelf->floor->warehouse_id,
                'floor_id' => $bin->shelf->floor_id,
                'shelf_id' => $bin->shelf_id,
                'bin_id' => $bin->id,
                'company_id' => auth()->user()->company_id
            ]);

            $powderLog->saveQuietly();

            Auth::logout();
        }
    }
}
