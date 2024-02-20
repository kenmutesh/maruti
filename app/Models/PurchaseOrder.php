<?php

namespace App\Models;

use App\Enums\DocumentLabelsEnum;
use App\Enums\PurchaseOrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  // use Traits\Syncer;

  protected $fillable = [
    'lpo_prefix',
    'lpo_suffix',
    'record_date',
    'due_date',
    'quotation_ref',
    'memo_ref',
    'invoice_ref',
    'delivery_ref',
    'sum_subtotal',
    'sum_vataddition',
    'sum_grandtotal',
    'amount_due',
    'currency',
    'discount',
    'terms',
    'status',
    'warehouse_id',
    'floor_id',
    'shelf_id',
    'bin_id',
    'supplier_id',
    'company_id'
  ];

  protected $casts = [
    'status' => PurchaseOrderStatusEnum::class,
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }

  public function purchaseorderdocuments()
  {
    return $this->hasMany(PurchaseOrderDocument::class);
  }

  public function purchaseorderitems()
  {
    return $this->hasMany(PurchaseOrderItem::class);
  }

  public function purchasepayments(){
    return $this->hasMany(PurchasePayment::class, 'purchase_order_id');
  }

  public function getNextPurchaseOrderPrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::PURCHASEORDER->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextPurchaseOrderSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::PURCHASEORDER->value,
      'company_id' => auth()->user()->company_id
    ])->first();


    $lastSuffix = PurchaseOrder::where([
      ['lpo_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('lpo_suffix');
    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getSubTotalAttribute()
  {
    $items = $this->purchaseorderitems->toArray();
    $subTotal = array_reduce($items, function ($accumulator, $item) {
      return $accumulator + ($item['sub_total']);
    }, 0);

    return $subTotal;
  }

  public function getVatAttribute()
  {
    $items = $this->purchaseorderitems->toArray();
    $vat = array_reduce($items, function ($accumulator, $item) {
      return $accumulator + ($item['vat_addition'] * $item['quantity']);
    }, 0);
    if ($this->discount > 0) {
      $vatRatio = ($vat/($vat + $this->sub_total));
      return ($vatRatio * (($vat + $this->sub_total) - $this->discount));
    } else {
      return $vat;
    }
  }

  public function getGrandTotalAttribute()
  {
    if ($this->discount > 0) {
      $items = $this->purchaseorderitems->toArray();
      $undiscountedVAT = array_reduce($items, function ($accumulator, $item) {
        return $accumulator + ($item['vat_addition'] * $item['quantity']);
      }, 0);
      return round((($undiscountedVAT + $this->sub_total) - $this->discount), 2);
    }else{
      return round((($this->vat + $this->sub_total) - $this->discount), 2);
    }
  }

  public function calculateAmountDue()
  {
    if($this->status === PurchaseOrderStatusEnum::CLOSED){
      $purchasePayments = $this->purchasepayments->toArray();
  
      $paidAmount = array_reduce($purchasePayments, function ($accumulator, $purchasePayment) {
        if ($purchasePayment['nullified_at']) {
          return 0;
        }else{
          return $accumulator += $purchasePayment['amount_applied'];
        }
      }, 0);

      $amountDue = ($this->grand_total - $paidAmount);

      $this->updateQuietly([
        'amount_due' => $amountDue,
      ]);
    }else{
      $this->updateQuietly([
        'amount_due' => 0,
      ]);
    }
  }

  public function updateAmounts()
  {
    $this->updateQuietly([
      'sum_subtotal' => $this->sub_total,
      'sum_vataddition' => $this->vat,
      'sum_grandtotal' => $this->grand_total
    ]);
  }
}
