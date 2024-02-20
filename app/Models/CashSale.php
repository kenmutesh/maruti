<?php

namespace App\Models;

use App\Enums\CoatingJobStatusEnum;
use App\Enums\DocumentLabelsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashSale extends Model
{
  use HasFactory;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  use SoftDeletes;
  // use Traits\Syncer;

  protected $fillable = [
    'cash_sale_prefix',
    'cash_sale_suffix',
    'ext_cash_sale_prefix',
    'ext_cash_sale_suffix',
    'customer_id',
    'discount',
    'cu_number_prefix',
    'cu_number_suffix',
    'external',
    'created_by',
    'cancelled_at',
    'company_id'
  ];

  // a cash sale can only belong to one company
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function coatingjobs()
  {
    $coatingJobs = $this->hasMany(CoatingJob::class);

    $coatingJobs->getQuery()->select('id', 'quotation_prefix', 'quotation_suffix', 'coating_prefix', 'coating_suffix');

    return $coatingJobs;
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function getNextCashSalePrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::CASHSALE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextCashSaleSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::CASHSALE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = CashSale::where([
      ['cash_sale_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('cash_sale_suffix');
    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getNextExtCashSalePrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::EXTCASHSALE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextExtCashSaleSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::EXTCASHSALE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = CashSale::where([
      ['ext_cash_sale_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('ext_cash_sale_suffix');
    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getNextCuPrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::KRACONTROLUNIT->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextCuSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::KRACONTROLUNIT->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastCashSaleSuffix = CashSale::where([
      ['cu_number_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('cu_number_suffix');

    $lastInvoiceSuffix = Invoice::where([
      ['cu_number_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('cu_number_suffix');

    if ($lastCashSaleSuffix && $lastInvoiceSuffix) {
      if ($lastInvoiceSuffix > $lastCashSaleSuffix) {
        return $lastInvoiceSuffix + 1;
      } else {
        return $lastCashSaleSuffix + 1;
      }
    } else {
      return $documentLabel->document_suffix + 1;
    }
  }

  public function getSubTotalAttribute()
  {
    $coatingJobs = $this->coatingjobs;
    $subTotal = 0;
    foreach ($coatingJobs as $coatingJob) {
      if ($coatingJob->status == CoatingJobStatusEnum::OPEN->value) {
        continue;
      }
      $subTotal += $coatingJob->sub_total;
    }

    return round($subTotal, 2);
  }

  public function getVatAdditionAttribute()
  {
    $coatingJobs = $this->coatingjobs;
    $vat = 0;
    foreach ($coatingJobs as $coatingJob) {
      if ($coatingJob->status == CoatingJobStatusEnum::OPEN->value) {
        continue;
      }
      $vat += $coatingJob->vat_addition;
    }

      return $vat;

  }

  public function getTotalAttribute()
  {
    $coatingJobs = $this->coatingjobs;
    $total = 0;
    foreach ($coatingJobs as $coatingJob) {
      if ($coatingJob->status == CoatingJobStatusEnum::OPEN->value) {
        continue;
      }
      $total += $coatingJob->grand_total;
    }

    return round($total, 2);
  }

  public function getGrandTotalAttribute()
  {
    $grandTotal = $this->total - $this->discount;

    return round($grandTotal);
  }

  public function getIsDirectAttribute()
  {
    $coatingJobs = $this->coatingjobs;
    $count = 0;
    foreach ($coatingJobs as $coatingJob) {
      if ($coatingJob->coating_suffix) {
        $count += 1;
      }
    }
    return ($count > 0) ? false : true;
  }
}
