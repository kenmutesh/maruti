<?php

namespace App\Models\Traits;

/**
 * 
 */
trait ModelTable
{
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
