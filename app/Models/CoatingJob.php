<?php

namespace App\Models;

use App\Enums\CoatingJobOwnerEnum;
use App\Enums\CoatingJobProfileTypesEnum;
use App\Enums\CoatingJobStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Enums\DocumentLabelsEnum;

class CoatingJob extends Model
{
  use HasFactory;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  // use Traits\Syncer;

  protected $fillable = [
    'coating_prefix',
    'coating_suffix',
    'quotation_prefix',
    'quotation_suffix',
    'customer_id',
    'cash_sale_name',
    'lpo',
    'date',
    'in_date',
    'ready_date',
    'out_date',
    'goods_weight',
    'profile_type',
    'powder_estimate',
    'powder_id',
    'belongs_to',
    'status',
    'sum_subtotal',
    'sum_vataddition',
    'sum_grandtotal',
    'prepared_by',
    'supervisor',
    'quality_by',
    'sale_by',
    'created_by',
    'cancelled_at',
    'invoice_id',
    'cash_sale_id',
    'company_id'
  ];

  protected $casts = [
    'status' => CoatingJobStatusEnum::class,
    'belongs_to' => CoatingJobOwnerEnum::class,
    'profile_type' => CoatingJobProfileTypesEnum::class,
  ];

  // a coating job belongs to one company
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  // a coating job belongs to one customer
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  // a job has one powder item
  public function powder()
  {
    return $this->belongsTo(Powder::class);
  }

  public function preparedBy()
  {
    return $this->hasOne(User::class, 'id', 'prepared_by');
  }

  public function createdBy()
  {
    return $this->hasOne(User::class, 'id', 'created_by');
  }

  public function marutiitems()
  {
    return $this->hasMany(CoatingJobMarutiItem::class, 'coating_job_id');
  }

  public function aluminiumitems()
  {
    return $this->hasMany(CoatingJobAluminiumItem::class);
  }

  public function steelitems()
  {
    return $this->hasMany(CoatingJobSteelItem::class);
  }

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function cashsale()
  {
    return $this->belongsTo(CashSale::class, 'cash_sale_id');
  }

  public function getNextCoatingJobPrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::COATING->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextCoatingJobSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::COATING->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = CoatingJob::where([
      ['coating_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('coating_suffix');

    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getNextQuotationPrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::QUOTATION->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextQuotationSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::QUOTATION->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = CoatingJob::where([
      ['quotation_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('quotation_suffix');

    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getSubTotalAttribute()
  {
    $subTotal = 0;

    $marutiItems = $this->marutiitems->toArray();

    $marutiItemsSubTotal = array_reduce($marutiItems, function ($accumulator, $item) {
      return $accumulator + $item['sub_total'];
    }, 0);

    $aluminiumItems = $this->aluminiumitems->toArray();

    $aluminiumItemsSubTotal = array_reduce($aluminiumItems, function ($accumulator, $item) {
      return $accumulator + $item['sub_total'];
    }, 0);

    $steelItemsSubTotal = 0;

    $steelItems = $this->steelitems->toArray();

    $steelItemsSubTotal = array_reduce($steelItems, function ($accumulator, $item) {
      return $accumulator + $item['sub_total'];
    }, 0);

    $subTotal += $marutiItemsSubTotal;

    $subTotal += $aluminiumItemsSubTotal;

    $subTotal += $steelItemsSubTotal;

    return round($subTotal, 2);
  }

  public function getVatAdditionAttribute()
  {
    $subTotal = 0;
    
    $marutiItems = $this->marutiitems->toArray();

    $marutiItemsVatAddition = array_reduce($marutiItems, function ($accumulator, $item) {
      return $accumulator + ($item['vat_addition'] * $item['quantity']);
    }, 0);

    $aluminiumItems = $this->aluminiumitems->toArray();

    $aluminiumItemVatAddition = array_reduce($aluminiumItems, function ($accumulator, $item) {
      return $accumulator + ($item['vat_addition'] * $item['item_kg']);
    }, 0);

    $steelItems = $this->steelitems->toArray();

    $steelItemVatAddition = array_reduce($steelItems, function ($accumulator, $item) {
      return $accumulator + ($item['vat_addition'] * $item['quantity']);
    }, 0);

    $subTotal += $marutiItemsVatAddition;

    $subTotal += $aluminiumItemVatAddition;

    $subTotal += $steelItemVatAddition;

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
