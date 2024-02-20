<?php

namespace App\Observers;

use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\CoatingJobMarutiItem;
use App\Models\InventoryItem;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;

class CoatingJobMarutiItemObserver
{
    public function created(CoatingJobMarutiItem $coatingJobMarutiItem)
    {
        if ($coatingJobMarutiItem->inventory_item_id) {
            $lastInventoryItemLog = PowderAndInventoryLog::where([
                ['inventory_item_id', '=', $coatingJobMarutiItem->inventory_item_id],
                ['sum_added', '>', 0]
            ])->orderBy('id', 'desc')->first();

            if ($lastInventoryItemLog) {
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $lastInventoryItemLog->warehouse_id,
                    'floor_id' => $lastInventoryItemLog->floor_id,
                    'shelf_id' => $lastInventoryItemLog->shelf_id,
                    'bin_id' => $lastInventoryItemLog->bin_id,
                ]);
                $coatingJobMarutiItem->updateQuietly();

                $inventoryItemLog = new PowderAndInventoryLog();

                $reason = PowderAndInventoryLogsEnum::DIRECTINVOICE;
                if($coatingJobMarutiItem->coatingjob->coating_suffix === null && $coatingJobMarutiItem->coatingjob->cash_sale_id){
                    $reason = PowderAndInventoryLogsEnum::DIRECTCASHALE;
                }
                if($coatingJobMarutiItem->coatingjob->coating_suffix){
                    $reason = PowderAndInventoryLogsEnum::COATINGJOB;
                }

                $inventoryItemLog->fill([
                    'reason' => $reason,
                    'reason_id' => $coatingJobMarutiItem->coating_job_id,
                    'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                    'inventory_item_id' => $coatingJobMarutiItem->inventory_item_id,
                    'warehouse_id' => $lastInventoryItemLog->warehouse_id,
                    'floor_id' => $lastInventoryItemLog->floor_id,
                    'shelf_id' => $lastInventoryItemLog->shelf_id,
                    'bin_id' => $lastInventoryItemLog->bin_id,
                    'company_id' => auth()->user()->company_id
                ]);
                $inventoryItemLog->save();
            } else {
                $bin = Bin::orderBy('id', 'desc')->first();
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id,
                ]);
                $coatingJobMarutiItem->updateQuietly();

                $inventoryItemLog = new PowderAndInventoryLog();
                
                $reason = PowderAndInventoryLogsEnum::DIRECTINVOICE;
                if($coatingJobMarutiItem->coatingjob->coating_suffix === null && $coatingJobMarutiItem->coatingjob->cash_sale_id){
                    $reason = PowderAndInventoryLogsEnum::DIRECTCASHALE;
                }
                if($coatingJobMarutiItem->coatingjob->coating_suffix){
                    $reason = PowderAndInventoryLogsEnum::COATINGJOB;
                }

                $inventoryItemLog->fill([
                    'reason' => $reason,
                    'reason_id' => $coatingJobMarutiItem->coating_job_id,
                    'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                    'inventory_item_id' => $coatingJobMarutiItem->inventory_item_id,
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id,
                    'company_id' => auth()->user()->company_id
                ]);
                $inventoryItemLog->save();
            }
        }

        if ($coatingJobMarutiItem->powder_id) {
            $lastPowderLog = PowderAndInventoryLog::where([
                ['powder_id', '=', $coatingJobMarutiItem->powder_id],
                ['sum_added', '>', 0]
            ])->orderBy('id', 'desc')->first();

            if ($lastPowderLog) {
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $lastPowderLog->warehouse_id,
                    'floor_id' => $lastPowderLog->floor_id,
                    'shelf_id' => $lastPowderLog->shelf_id,
                    'bin_id' => $lastPowderLog->bin_id,
                ]);
                $coatingJobMarutiItem->updateQuietly();

                $powderLog = new PowderAndInventoryLog();

                $reason = PowderAndInventoryLogsEnum::DIRECTINVOICE;
                if($coatingJobMarutiItem->coatingjob->coating_suffix === null && $coatingJobMarutiItem->coatingjob->cash_sale_id){
                    $reason = PowderAndInventoryLogsEnum::DIRECTCASHALE;
                }
                if($coatingJobMarutiItem->coatingjob->coating_suffix){
                    $reason = PowderAndInventoryLogsEnum::COATINGJOB;
                }

                $powderLog->fill([
                    'reason' => $reason,
                    'reason_id' => $coatingJobMarutiItem->coating_job_id,
                    'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                    'powder_id' => $lastPowderLog->powder_id,
                    'warehouse_id' => $lastPowderLog->warehouse_id,
                    'floor_id' => $lastPowderLog->floor_id,
                    'shelf_id' => $lastPowderLog->shelf_id,
                    'bin_id' => $lastPowderLog->bin_id,
                    'company_id' => auth()->user()->company_id
                ]);
                $powderLog->save();
            } else {
                $bin = Bin::orderBy('id', 'desc')->first();
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id,
                ]);
                $coatingJobMarutiItem->updateQuietly();

                $powderLog = new PowderAndInventoryLog();
                
                $reason = PowderAndInventoryLogsEnum::DIRECTINVOICE;
                if($coatingJobMarutiItem->coatingjob->coating_suffix === null && $coatingJobMarutiItem->coatingjob->cash_sale_id){
                    $reason = PowderAndInventoryLogsEnum::DIRECTCASHALE;
                }
                if($coatingJobMarutiItem->coatingjob->coating_suffix){
                    $reason = PowderAndInventoryLogsEnum::COATINGJOB;
                }
                
                $powderLog->fill([
                    'reason' => $reason,
                    'reason_id' => $coatingJobMarutiItem->coating_job_id,
                    'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                    'powder_id' => $lastPowderLog->powder_id,
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id,
                    'company_id' => auth()->user()->company_id
                ]);
                $powderLog->save();
            }
        }
    }

    public function updated(CoatingJobMarutiItem $coatingJobMarutiItem)
    {
        if ($coatingJobMarutiItem->inventory_item_id) {
            $inventoryLog = PowderAndInventoryLog::where([
                ['reason_id', '=', $coatingJobMarutiItem->coating_job_id],
            ])->first();

            $originalSumAdded = $inventoryLog->sum_added;

            $inventoryItem = InventoryItem::find($coatingJobMarutiItem->inventory_item_id);

            $inventoryItem->current_quantity += $originalSumAdded * -1; // make it a positive value

            $inventoryItem->update();

            $lastInventoryItemLog = PowderAndInventoryLog::where([
                ['inventory_item_id', '=', $coatingJobMarutiItem->inventory_item_id],
                ['sum_added', '>', 0]
            ])->orderBy('id', 'desc')->first();

            if ($lastInventoryItemLog) {
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $lastInventoryItemLog->warehouse_id,
                    'floor_id' => $lastInventoryItemLog->floor_id,
                    'shelf_id' => $lastInventoryItemLog->shelf_id,
                    'bin_id' => $lastInventoryItemLog->bin_id,
                ]);
                if($inventoryLog){
                    $inventoryLog->fill([
                        'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                        'inventory_item_id' => $coatingJobMarutiItem->inventory_item_id,
                        'warehouse_id' => $lastInventoryItemLog->warehouse_id,
                        'floor_id' => $lastInventoryItemLog->floor_id,
                        'shelf_id' => $lastInventoryItemLog->shelf_id,
                        'bin_id' => $lastInventoryItemLog->bin_id,
                        'company_id' => auth()->user()->company_id
                    ]);
                }
            }else{
                $bin = Bin::orderBy('id', 'desc')->first();
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id,
                ]);
                if($inventoryLog){
                    $inventoryLog->fill([
                        'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                        'inventory_item_id' => $coatingJobMarutiItem->inventory_item_id,
                        'warehouse_id' => $bin->shelf->floor->warehouse_id,
                        'floor_id' => $bin->shelf->floor_id,
                        'shelf_id' => $bin->shelf_id,
                        'bin_id' => $bin->id,
                        'company_id' => auth()->user()->company_id
                    ]);
                }
            }
            
            $coatingJobMarutiItem->updateQuietly();
            if($inventoryLog){
                $inventoryLog->update();
            }
        }

        if ($coatingJobMarutiItem->powder_id) {
            $powderLog = PowderAndInventoryLog::where([
                ['reason_id', '=', $coatingJobMarutiItem->coating_job_id],
            ])->first();

            $originalSumAdded = $powderLog->sum_added;

            $powder = Powder::find($coatingJobMarutiItem->powder_id);

            $powder->current_weight += $originalSumAdded * -1; // make it a positive value

            $powder->update();

            $lastPowderLog = PowderAndInventoryLog::where([
                ['powder_id', '=', $coatingJobMarutiItem->powder_id],
                ['sum_added', '>', 0]
            ])->orderBy('id', 'desc')->first();

            if ($lastPowderLog) {
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $lastPowderLog->warehouse_id,
                    'floor_id' => $lastPowderLog->floor_id,
                    'shelf_id' => $lastPowderLog->shelf_id,
                    'bin_id' => $lastPowderLog->bin_id,
                ]);
                if($powderLog){
                    $powderLog->fill([
                        'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                        'powder_id' => $coatingJobMarutiItem->powder_id,
                        'warehouse_id' => $lastPowderLog->warehouse_id,
                        'floor_id' => $lastPowderLog->floor_id,
                        'shelf_id' => $lastPowderLog->shelf_id,
                        'bin_id' => $lastPowderLog->bin_id,
                        'company_id' => auth()->user()->company_id
                    ]);
                }
            }else{
                $bin = Bin::orderBy('id', 'desc')->first();
                $coatingJobMarutiItem->fill([
                    'warehouse_id' => $bin->shelf->floor->warehouse_id,
                    'floor_id' => $bin->shelf->floor_id,
                    'shelf_id' => $bin->shelf_id,
                    'bin_id' => $bin->id,
                ]);
                if($powderLog){
                    $powderLog->fill([
                        'sum_added' => ($coatingJobMarutiItem->quantity * -1),
                        'powder_id' => $coatingJobMarutiItem->powder_id,
                        'warehouse_id' => $bin->shelf->floor->warehouse_id,
                        'floor_id' => $bin->shelf->floor_id,
                        'shelf_id' => $bin->shelf_id,
                        'bin_id' => $bin->id,
                        'company_id' => auth()->user()->company_id
                    ]);
                }
            }
            
            $coatingJobMarutiItem->updateQuietly();
            if($powderLog){
                $powderLog->update();
            }
        }
    }

    public function deleted(CoatingJobMarutiItem $coatingJobMarutiItem)
    {
        //
    }

    public function restored(CoatingJobMarutiItem $coatingJobMarutiItem)
    {
        //
    }

    public function forceDeleted(CoatingJobMarutiItem $coatingJobMarutiItem)
    {
        //
    }
}
