<?php

namespace App\Observers;

use App\Models\InventoryItem;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;

class PowderAndInventoryLogObserver
{
    /**
     * Handle the PowderAndInventoryLog "created" event.
     *
     * @param  \App\Models\PowderAndInventoryLog  $powderAndInventoryLog
     * @return void
     */
    public function created(PowderAndInventoryLog $powderAndInventoryLog)
    {
        if($powderAndInventoryLog->powder_id){
            $powder = Powder::find($powderAndInventoryLog->powder_id);
            $powder->current_weight += $powderAndInventoryLog->sum_added;
            $powder->update();
        }

        if($powderAndInventoryLog->inventory_item_id){
            $inventoryItem = InventoryItem::find($powderAndInventoryLog->inventory_item_id);
            $inventoryItem->current_quantity += $powderAndInventoryLog->sum_added;
            $inventoryItem->update();
        }
    }

    /**
     * Handle the PowderAndInventoryLog "updated" event.
     *
     * @param  \App\Models\PowderAndInventoryLog  $powderAndInventoryLog
     * @return void
     */
    public function updated(PowderAndInventoryLog $powderAndInventoryLog)
    {
        if($powderAndInventoryLog->powder_id){
            $powder = Powder::find($powderAndInventoryLog->powder_id);
            $powder->current_weight += $powderAndInventoryLog->sum_added;
            $powder->update();
        }

        if($powderAndInventoryLog->inventory_item_id){
            $inventoryItem = InventoryItem::find($powderAndInventoryLog->inventory_item_id);
            $inventoryItem->current_quantity += $powderAndInventoryLog->sum_added;
            $inventoryItem->update();
        }
    }

    /**
     * Handle the PowderAndInventoryLog "deleted" event.
     *
     * @param  \App\Models\PowderAndInventoryLog  $powderAndInventoryLog
     * @return void
     */
    public function deleted(PowderAndInventoryLog $powderAndInventoryLog)
    {
        //
    }

    /**
     * Handle the PowderAndInventoryLog "restored" event.
     *
     * @param  \App\Models\PowderAndInventoryLog  $powderAndInventoryLog
     * @return void
     */
    public function restored(PowderAndInventoryLog $powderAndInventoryLog)
    {
        //
    }

    /**
     * Handle the PowderAndInventoryLog "force deleted" event.
     *
     * @param  \App\Models\PowderAndInventoryLog  $powderAndInventoryLog
     * @return void
     */
    public function forceDeleted(PowderAndInventoryLog $powderAndInventoryLog)
    {
        //
    }
}
