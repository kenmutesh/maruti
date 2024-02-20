<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompanyScope;

/**
 * 
 */
trait CompanyFilter
{
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
