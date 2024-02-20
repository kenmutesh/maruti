<?php

namespace App\Models;

use App\Enums\DocumentLabelsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;

  protected $fillable = [
    'invoice_prefix',
    'invoice_suffix',
    'ext_invoice_prefix',
    'ext_invoice_suffix',
    'customer_id',
    'amount_due',
    'discount',
    'cu_number_prefix',
    'cu_number_suffix',
    'external',
    'created_by',
    'cancelled_at',
    'company_id'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  // an invoice can only belong to one customer
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function coatingjobs()
  {
    return $this->hasMany(CoatingJob::class);
  }

  public function payments()
  {
    return $this->belongsToMany(Payment::class, InvoicePayment::getTableName());
  }

  public function invoicepayments()
  {
    return $this->hasMany(InvoicePayment::class);
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function getNextInvoicePrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::INVOICE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextInvoiceSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::INVOICE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = Invoice::where([
      ['invoice_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('invoice_suffix');
    if ($lastSuffix != null) {
      return $lastSuffix + 1;
    }
    return $documentLabel->document_suffix + 1;
  }

  public function getNextExtInvoicePrefixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_prefix')->where([
      'document' => DocumentLabelsEnum::EXTINVOICE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    return $documentLabel->document_prefix;
  }

  public function getNextExtInvoiceSuffixAttribute()
  {
    $documentLabel = DocumentLabel::select('document_suffix')->where([
      'document' => DocumentLabelsEnum::EXTINVOICE->value,
      'company_id' => auth()->user()->company_id
    ])->first();

    $lastSuffix = Invoice::where([
      ['ext_invoice_suffix', '>=', $documentLabel->document_suffix],
      ['company_id', '=', auth()->user()->company_id]
    ])->max('ext_invoice_suffix');
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
    $coatingJobs = CoatingJob::select('id', 'sum_subtotal')->where('invoice_id', $this->id)->get();
    $subTotal = 0;
    foreach ($coatingJobs as $coatingJob) {
      $subTotal += $coatingJob->subtotal;
    }

    return round($subTotal, 2);
  }

  public function getVatAdditionAttribute()
  {
    $coatingJobs = CoatingJob::select('id', 'sum_vataddition')->where('invoice_id', $this->id)->get();
    $vat = 0;
    foreach ($coatingJobs as $coatingJob) {
      $vat += $coatingJob->vataddition;
    }

    return $vat;
  }

  public function getTotalAttribute()
  {
    $coatingJobs = CoatingJob::select('id', 'sum_grandtotal')->where('invoice_id', $this->id)->get();
    $total = 0;
    foreach ($coatingJobs as $coatingJob) {
      $total += $coatingJob->sum_grandtotal;
    }

    return round($total, 2);
  }

  public function getGrandTotalAttribute()
  {
    $grandTotal = $this->total - $this->discount;

    return round($grandTotal);
  }

  public function calculateAmountDue()
  {
    $invoicePayments = $this->invoicepayments->toArray();

    $paidAmount = array_reduce($invoicePayments, function ($accumulator, $invoicePayment) {
      if ($invoicePayment['nullified_at']) {
        return 0;
      } else {
        return $accumulator += $invoicePayment['amount_applied'];
      }
    }, 0);

    $amountDue = ($this->grand_total - $paidAmount);
    $this->updateQuietly([
      'amount_due' => $amountDue,
    ]);
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
