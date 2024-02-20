<?php

namespace App\Models;

use App\Enums\PowderAndInventoryLogsEnum;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowderAndInventoryLog extends Model
{
  use HasFactory, HasEvents;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  // use Traits\Syncer;

  protected $fillable = [
    'reason',
    'reason_id',
    'sum_added',
    'powder_id',
    'inventory_item_id',
    'non_inventory_item_id',
    'warehouse_id',
    'floor_id',
    'shelf_id',
    'bin_id',
    'company_id'
  ];

  protected $casts = [
    'reason' => PowderAndInventoryLogsEnum::class,
  ];

  public function powder()
  {
    return $this->belongsTo(Powder::class);
  }

  public function inventoryitem()
  {
    return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
  }
}
