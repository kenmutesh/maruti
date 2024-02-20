<?php

namespace App\Observers;

use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\CoatingJob;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;

class CoatingJobObserver
{
    public function created(CoatingJob $coatingjob)
    {
        $powderLog = new PowderAndInventoryLog();
        if ($coatingjob->powder_id) {
            $lastPowderLog = PowderAndInventoryLog::where([
                ['powder_id', '=', $coatingjob->powder_id],
                ['sum_added', '>', 0]
            ])->orderBy('id', 'desc')->first();
            if ($lastPowderLog) {
                $powderLog->fill([
                    'reason' => PowderAndInventoryLogsEnum::COATINGJOB,
                    'reason_id' => $coatingjob->id,
                    'sum_added' => ($coatingjob->powder_estimate * -1),
                    'powder_id' => $coatingjob->powder_id,
                    'warehouse_id' => $lastPowderLog->warehouse_id,
                    'floor_id' => $lastPowderLog->floor_id,
                    'shelf_id' => $lastPowderLog->shelf_id,
                    'bin_id' => $lastPowderLog->bin_id,
                    'company_id' => $coatingjob->company_id
                ]);
            } else {
                $bin = Bin::orderBy('id')->with(['shelf.floor.warehouse'])->first();
                $powderLog->fill([
                    'reason' => PowderAndInventoryLogsEnum::COATINGJOB,
                    'reason_id' => $coatingjob->id,
                    'sum_added' => ($coatingjob->powder_estimate * -1),
                    'powder_id' => $coatingjob->powder_id,
                    'warehouse_id' => $bin->shelf->floor->warehouse->id,
                    'floor_id' => $bin->shelf->floor->id,
                    'shelf_id' => $bin->shelf->id,
                    'bin_id' => $bin->id,
                    'company_id' => $coatingjob->company_id
                ]);
            }
            $powderLog->save();
        }
    }

    public function updated(CoatingJob $coatingjob)
    {
        if ($coatingjob->powder_id) {
            $powder = Powder::where([
                ['id', '=', $coatingjob->powder_id],
            ])->first();

            $powderLog = PowderAndInventoryLog::where([
                ['reason_id', '=', $coatingjob->id],
            ])->first();
            
            $powder->current_weight += (($powderLog->sum_added ?? 0) * -1) + ($coatingjob->powder_estimate * -1);
            
            $powder->update();

            $lastPowderLog = PowderAndInventoryLog::where([
                ['powder_id', '=', $coatingjob->powder_id],
                ['sum_added', '>', 0]
            ])->orderBy('id', 'desc')->first();

            if ($lastPowderLog) {
                if ($powderLog) {
                    $powderLog->fill([
                        'sum_added' => ($coatingjob->powder_estimate * -1),
                        'powder_id' => $coatingjob->powder_id,
                        'warehouse_id' => $lastPowderLog->warehouse_id,
                        'floor_id' => $lastPowderLog->floor_id,
                        'shelf_id' => $lastPowderLog->shelf_id,
                        'bin_id' => $lastPowderLog->bin_id,
                    ]);
                    $powderLog->update();
                }
            } else {
                $bin = Bin::orderBy('id')->with(['shelf.floor.warehouse'])->first();
                if ($powderLog) {
                    $powderLog->fill([
                        'reason' => PowderAndInventoryLogsEnum::COATINGJOB,
                        'reason_id' => $coatingjob->id,
                        'sum_added' => ($coatingjob->powder_estimate * -1),
                        'powder_id' => $coatingjob->powder_id,
                        'warehouse_id' => $bin->shelf->floor->warehouse->id,
                        'floor_id' => $bin->shelf->floor->id,
                        'shelf_id' => $bin->shelf->id,
                        'bin_id' => $bin->id,
                        'company_id' => $coatingjob->company_id
                    ]);
                    $powderLog->update();
                }
            }
        }
    }

    public function deleted(CoatingJob $coatingjob)
    {
        //
    }

    public function restored(CoatingJob $coatingjob)
    {
        //
    }

    public function forceDeleted(CoatingJob $coatingjob)
    {
        //
    }
}
