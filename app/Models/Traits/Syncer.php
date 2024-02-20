<?php

namespace App\Models\Traits;

use App\Models\Sync;

/**
 * 
 */
trait Syncer
{
    public static function booted() {
        static::created(function ($currentModel) {
            $syncer = new Sync();
            $syncer->action_name = "INSERT";
            $syncer->table_name = $currentModel->getTable();
            $syncer->row_id = $currentModel->id;
            $syncer->company_id = session()->get('auth_company_uid');
            $syncer->save();
        });
        static::updated(function ($currentModel) {
            $syncer = new Sync();
            $syncer->action_name = "UPDATE";
            $syncer->table_name = $currentModel->getTable();
            $syncer->row_id = $currentModel->id;
            $syncer->company_id = session()->get('auth_company_uid');
            $syncer->save();
        });
        static::deleted(function ($currentModel) {
            $syncer = new Sync();
            $syncer->action_name = "DELETE";
            $syncer->table_name = $currentModel->getTable();
            $syncer->row_id = $currentModel->id;
            $syncer->company_id = session()->get('auth_company_uid');
            $syncer->save();
        });
    }
}
