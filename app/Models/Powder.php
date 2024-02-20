<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Powder extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;

  protected $fillable = [
    'powder_color',
    'powder_code',
    'powder_description',
    'serial_no',
    'manufacture_date',
    'expiry_date',
    'goods_weight',
    'batch_no',
    'standard_cost',
    'standard_cost_vat',
    'standard_price',
    'standard_price_vat',
    'min_threshold',
    'max_threshold',
    'current_weight',
    'opening_weight',
    'supplier_id',
    'company_id'
  ];

  protected $appends = [
    'standard_cost_without_vat',
    'standard_price_without_vat',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }

  public function powderlogs()
  {
    return $this->hasMany(PowderAndInventoryLog::class, 'powder_id');
  }

  public function getStandardCostWithoutVatAttribute()
  {
    $vatPercentage = (100 + $this->standard_cost_vat) / 100;
    $cost = $this->standard_cost / $vatPercentage;
    return round($cost, 2);
  }

  public function getStandardPriceWithoutVatAttribute()
  {
    $vatPercentage = (100 + $this->standard_cost_vat) / 100;
    $cost = $this->standard_price / $vatPercentage;
    return round($cost, 2);
  }
}
