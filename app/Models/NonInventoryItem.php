<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonInventoryItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;

    protected $fillable = [
        'item_name',
        'standard_cost',
        'standard_cost_vat',
        'supplier_id',
        'company_id'
    ];

    protected $appends = [
      'standard_cost_without_vat',
    ];

    public function supplier()
    {
      return $this->belongsTo(Supplier::class);
    }

    public function getStandardCostWithoutVatAttribute()
    {
      $vatPercentage = (100 + $this->standard_cost_vat)/100;
      $cost = $this->standard_cost/$vatPercentage;
      return round($cost, 2); 
    }
}
