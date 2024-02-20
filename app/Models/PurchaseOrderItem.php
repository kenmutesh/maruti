<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
  use HasFactory;
  use Traits\ModelTable;
  // use Traits\Syncer;

  protected $fillable = [
    'purchase_order_id',
    'new_item_name',
    'powder_id',
    'inventory_item_id',
    'non_inventory_item_id',
    'cost',
    'vat',
    'quantity'
  ];

  protected $appends = [
    'vat_addition',
    'sub_total'
];

  public function powder()
  {
    return $this->belongsTo(Powder::class);
  }

  public function inventoryitem()
  {
    return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
  }

  public function noninventoryitem()
  {
    return $this->belongsTo(NonInventoryItem::class, 'non_inventory_item_id');
  }

  public function getVatAdditionAttribute()
  {
    $vatAddition = $this->cost * ($this->vat / 100);
    return round($vatAddition, 2);
  }

  public function getSubTotalAttribute()
  {
    return round(($this->cost * $this->quantity), 2);
  }
}
