<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoatingJobMarutiItem extends Model
{
    use HasFactory;
    use Traits\ModelTable;
    use SoftDeletes;

    protected $fillable = [
        'coating_job_id',
        'inventory_item_id',
        'powder_id',
        'custom_item_name',
        'warehouse_id',
        'uom',
        'floor_id',
        'shelf_id',
        'bin_id',
        'unit_price',
        'quantity',
        'vat',
        'vat_inclusive'
    ];

    protected $appends = [
        'unit_price_without_vat',
        'unit_price_with_vat',
        'vat_addition',
        'sub_total',
        'total'
    ];

    public function inventoryitem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function powder()
    {
        return $this->belongsTo(Powder::class);
    }

    public function coatingjob()
    {
        return $this->belongsTo(CoatingJob::class, 'coating_job_id');
    }

    public function getVatAdditionAttribute()
    {
        $vatAddition = ($this->unit_price * ($this->vat/100));

        if($this->vat_inclusive){
            $vatAddition = ($this->unit_price * ($this->vat/100))/((100 + $this->vat)/100);
        }
        return round($vatAddition, 2);
    }

    public function getUnitPriceWithoutVatAttribute()
    {
        $unitPriceWithoutVAT = $this->unit_price;
        if($this->vat_inclusive){
            $vat = (100 + $this->vat)/100;
            $unitPriceWithoutVAT = $this->unit_price/$vat;
        }

        return round($unitPriceWithoutVAT, 2);
    }

    public function getUnitPriceWithVatAttribute()
    {
        $unitPriceWithVAT = $this->unit_price;
        if(!$this->vat_inclusive){
            $vat = (100 + $this->vat)/100;
            $unitPriceWithVAT = $this->unit_price * $vat;
        }

        return round($unitPriceWithVAT, 2);
    }

    public function getSubTotalAttribute()
    {  
        $subTotal = $this->unit_price_without_vat * $this->quantity;

        return round($subTotal, 2);
    }

    public function getTotalAttribute()
    {
        $total = ($this->vat_addition * $this->quantity) + $this->sub_total;
        return round($total, 2);
    }
}
