<?php

namespace App\Models;

use App\Enums\InventoryItemsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;

  protected $fillable = [
    'type',
    'item_name',
    'item_code',
    'item_description',
    'serial_no',
    'quantity_tag',
    'goods_weight',
    'standard_cost',
    'standard_cost_vat',
    'standard_price',
    'standard_price_vat',
    'min_threshold',
    'max_threshold',
    'current_quantity',
    'opening_quantity',
    'supplier_id',
    'company_id'
  ];

  protected $casts = [
    'type' => InventoryItemsEnum::class,
  ];

  protected $appends = [
    'standard_cost_without_vat',
    'standard_price_without_vat',
  ];

  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
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
