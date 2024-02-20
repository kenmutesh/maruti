<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoatingJobAluminiumItem extends Model
{
    use HasFactory;
    use Traits\ModelTable;
    use SoftDeletes;

    protected $fillable = [
        'coating_job_id',
        'item_name',
        'uom',
        'item_kg',
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

    public function getVatAdditionAttribute()
    {
        $vatAddition = ($this->unit_price * ($this->vat/100));

        if($this->vat_inclusive){
            $vatAddition = ($this->unit_price * ($this->vat/100))/((100 + $this->vat)/100);
        }

        return $vatAddition;
    }

    public function getUnitPriceWithoutVatAttribute()
    {
        $unitPriceWithoutVAT = $this->unit_price;
        if($this->vat_inclusive){
            $vat = (100 + $this->vat)/100;
            $unitPriceWithoutVAT = $this->unit_price/$vat;
        }

        return $unitPriceWithoutVAT;
    }

    public function getUnitPriceWithVatAttribute()
    {
        $unitPriceWithVAT = $this->unit_price;
        if(!$this->vat_inclusive){
            $vat = (100 + $this->vat)/100;
            $unitPriceWithVAT = $this->unit_price * $vat;
        }

        return $unitPriceWithVAT;
    }

    public function getSubTotalAttribute()
    {  
        $subTotal = $this->unit_price_without_vat * $this->item_kg ;

        return $subTotal;
    }

    public function getTotalAttribute()
    {
        $total = ($this->vat_addition * $this->item_kg) + $this->sub_total;
        return $total;
    }
}
