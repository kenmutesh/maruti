<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;

    protected $fillable = [
        'supplier_payment_id',
        'purchase_order_id',
        'amount_applied',
        'nullified_at'
    ];

    public function purchaseorder(){
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
      }
    
      public function payment(){
        return $this->belongsTo(Payment::class);
      }
}
