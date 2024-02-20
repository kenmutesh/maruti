<?php

namespace App\Models;

use App\Enums\PaymentModesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  // use Traits\Syncer;

  protected $fillable = [
    'customer_id',
    'created_by',
    'payment_mode',
    'transaction_ref',
    'sum_invoice_payments',
    'payment_date',
    'nullified_at',
    'company_id'
  ];

  protected $casts = [
    'payment_mode' => PaymentModesEnum::class,
  ];

  // payment belongs to one company
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  // pyment belongs to one customer
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function invoicepayments()
  {
    return $this->hasMany(InvoicePayment::class);
  }

  public function getPaidAmountAttribute()
  {
    $payments = $this->invoicepayments->toArray();
    $paidAmount = array_reduce($payments, function($accumulator, $payment){
      return $accumulator += $payment['amount_applied'];
    }, 0);

    return round($paidAmount, 2);
  }

  public function updateAmounts()
  {
    $this->updateQuietly([
      'sum_invoice_payments' => $this->paid_amount,
    ]);
  }
}
