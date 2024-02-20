<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayment extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  // use Traits\Syncer;

  protected $fillable = [
    'payment_id',
    'invoice_id',
    'amount_applied',
    'nullified_at'
  ];

  public function invoice(){
    return $this->belongsTo(Invoice::class);
  }

  public function payment(){
    return $this->belongsTo(Payment::class);
  }
}
