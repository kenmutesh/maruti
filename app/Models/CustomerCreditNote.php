<?php

namespace App\Models;

use App\Enums\DocumentLabelsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCreditNote extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  // use Traits\Syncer;

  protected $fillable = [
    'credit_note_prefix',
    'credit_note_suffix',
    'customer_id',
    'invoice_id',
    'record_date',
    'company_id',
    'sum_subtotal',
    'sum_vataddition',
    'sum_grandtotal',
    'memo'
  ];

  public function creditnoteitems()
  {
    return $this->hasMany(CustomerCreditNoteItem::class, 'customer_credit_note_id');
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function getNextCreditNotePrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::CREDITNOTE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextCreditNoteSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::CREDITNOTE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = CustomerCreditNote::where([
      ['credit_note_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('credit_note_suffix');

    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getSubTotalAttribute()
  {
    $subTotal = 0;

    $creditNoteItems = $this->creditnoteitems->toArray();

    $creditNoteItemsSubTotal = array_reduce($creditNoteItems, function ($accumulator, $item) {
      return $accumulator + $item['sub_total'];
    }, 0);

    $subTotal += $creditNoteItemsSubTotal;

    return round($subTotal, 2);
  }

  public function getVatAdditionAttribute()
  {
    $subTotal = 0;

    $creditNoteItems = $this->creditnoteitems->toArray();

    $creditNoteItemsVatAddition = array_reduce($creditNoteItems, function ($accumulator, $item) {
      return $accumulator + ($item['vat_addition'] * $item['quantity']);
    }, 0);

    $subTotal += $creditNoteItemsVatAddition;

    return round($subTotal, 2);
  }

  public function getGrandTotalAttribute()
  {
    $total = $this->sub_total + $this->vat_addition;
    return round($total);
  }

  public function updateAmounts()
  {
    $this->updateQuietly([
      'sum_subtotal' => $this->sub_total,
      'sum_vataddition' => $this->vat_addition,
      'sum_grandtotal' => $this->grand_total
    ]);
  }
}
