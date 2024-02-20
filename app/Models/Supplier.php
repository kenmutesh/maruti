<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Supplier extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;

  protected $fillable = [
    'supplier_name',
    'supplier_email',
    'supplier_mobile',
    'company_location',
    'company_pin',
    'company_box',
    'company_id',
    'opening_balance'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function purchaseorders()
  {
    return $this->hasMany(PurchaseOrder::class)->where('status', PurchaseOrderStatusEnum::CLOSED);
  }

  public function creditnotes()
  {
    return $this->hasMany(SupplierCreditNote::class);
  }

  public function powders()
  {
    return $this->hasMany(Powder::class);
  }

  public function getOpeningBalance($toDate)
  {
    $dateTo = Carbon::now()->format('Y-m-d');
    if ($toDate) {
      $dateTo = Carbon::parse($toDate)->format('Y-m-d');
    }

    $supplierOpeningBalanceAmountDue = $this->getOpeningBalanceAmountDue($dateTo);

    $purchasesOpeningBalance = $this->getPurchasesAmountDue($dateTo);

    return round(($supplierOpeningBalanceAmountDue + $purchasesOpeningBalance), 2);
  }

  private function getOpeningBalanceAmountDue($dateTo)
  {
    $cacheName = 'period_purchase_payments_' . $dateTo;

    if (Carbon::parse($this->created_at)->diffInDays($dateTo) == 0) {
      $payments = Cache::remember($cacheName, (60 * 3), function () use ($dateTo) {
        return SupplierPayment::where('supplier_id', $this->id)
          ->with([
            'purchasepayments' => function ($query) use ($dateTo) {
              $query->whereNull('purchase_order_id')->whereNull('nullified_at')->whereDate('created_at', '<=', $dateTo);
            }
          ])->get()->toArray();
      });

      $paidAmount = array_reduce($payments, function ($accumulator, $payment) {
        $amount = 0;
        foreach ($payment['purchasepayments'] as $purchasePayment) {
          $amount += $purchasePayment['amount_applied'];
        }
        return $accumulator += $amount;
      }, 0);

      return floatval($this->opening_balance) - $paidAmount;
    }
    return 0;
  }

  private function getPurchasesAmountDue($dateTo)
  {
    $cacheName = 'purchases_amount_due' . $this->id . '_' . $dateTo;

    $purchases = Cache::remember($cacheName, (60 * 30), function () use ($dateTo) {
      return PurchaseOrder::select('id', 'supplier_id', 'created_at', 'sum_grandtotal')
        ->with([
          'purchasepayments' => function ($query) use ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
          }
        ])
        ->where('supplier_id', $this->id)
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->where('amount_due', '>', 0)
        ->whereDate('created_at', '<=', $dateTo)
        ->orderBy('supplier_id')
        ->get();
    });

    $amountDue = 0;

    foreach ($purchases as $purchase) {
      $amountDue += $purchase->amount_due;
    }

    return $amountDue;
  }

  public function singleDayPurchasesAmountDue($date)
  {
    $carbonDateFormat = Carbon::now()->format('Y-m-d');
    if ($date) {
      $carbonDateFormat = Carbon::parse($date)->format('Y-m-d');
    }

    $purchaseResult = Cache::remember('single_day_purchases_' . $this->id . '_' . $carbonDateFormat, (60 * 3), function () use ($carbonDateFormat) {
      $purchases = PurchaseOrder::select('id', 'supplier_id', 'due_date', 'status', 'sum_grandtotal')
        ->where('supplier_id', $this->id)
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->whereDate('due_date', $carbonDateFormat);
      return $purchases->get();
    });

    $amountDue = 0;

    foreach ($purchaseResult as $purchase) {
      $cacheName = 'purchase_payments_' . $purchase->id . '_' . $carbonDateFormat;

      $paidAmount = Cache::remember($cacheName, (60 * 3), function () use ($carbonDateFormat, $purchase) {
        return PurchasePayment::where('purchase_order_id', $purchase->id)->whereDate('created_at', $carbonDateFormat)->whereNull('nullified_at')->sum('amount_applied');
      });

      $amountDue += ($purchase->sum_grandtotal - floatval($paidAmount));
    }

    if (Carbon::parse($this->created_at)->diffInDays($carbonDateFormat) == 0) {
      $amountDue += $this->getOpeningBalanceAmountDue($carbonDateFormat);
    }

    return $amountDue;
  }

  public function dateRangePeriodPurchasesAmountDue($date, $from, $to)
  {
    $finalDate = Carbon::now()->format('Y-m-d');
    $dateTo = Carbon::now()->subDays($to)->format('Y-m-d');
    $dateFrom = Carbon::now()->subDays($from)->format('Y-m-d');

    if ($date) {
      $finalDate = Carbon::parse($date)->format('Y-m-d');
    }
    if ($from) {
      $dateFrom = Carbon::parse($date)->subDays($from)->format('Y-m-d');
    }
    if ($to) {
      $dateTo = Carbon::parse($date)->subDays($to)->format('Y-m-d');
    }

    $cacheName = 'period_purchases_' . $this->id . '_' . $dateTo . '_' . $dateFrom . '_' . $finalDate;

    $purchaseResult = Cache::remember($cacheName, (60 * 3), function () use ($dateTo, $dateFrom) {
      $purchases = PurchaseOrder::select('id', 'supplier_id', 'due_date', 'status', 'sum_grandtotal')
        ->where('supplier_id', $this->id)
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->whereBetween('due_date', [$dateTo, $dateFrom]);

      return $purchases->get();
    });

    $amountDue = 0;

    foreach ($purchaseResult as $purchase) {

      $cacheName = 'period_purchase_payments_' . $purchase->id . '_' . $finalDate;

      $paidAmount = Cache::remember($cacheName, (60 * 3), function () use ($finalDate, $purchase) {
        return PurchasePayment::where('purchase_order_id', $purchase->id)->whereDate('created_at', '<=', $finalDate)->whereNull('nullified_at')->sum('amount_applied');
      });

      $amountDue += ($purchase->sum_grandtotal - floatval($paidAmount));
    }

    if (Carbon::parse($this->created_at)->diffInDays($finalDate, false) >= $from && Carbon::parse($this->created_at)->diffInDays($finalDate) < $to) {
      $amountDue += $this->getOpeningBalanceAmountDue($finalDate);
    }

    return $amountDue;
  }

  public function singlePeriodOverPurchasesAmountDue($date, $days)
  {
    $finalDate = Carbon::now()->format('Y-m-d');
    $dateFrom = Carbon::now()->subDays($days)->format('Y-m-d');
    if ($date) {
      $dateFrom = Carbon::parse($date)->subDays($days)->format('Y-m-d');
      $finalDate = Carbon::parse($date)->format('Y-m-d');
    }

    $purchases = Cache::remember('single_day_over_invoices_' . $this->id . '_' . $dateFrom, (60 * 3), function () use ($dateFrom) {
      $purchases = PurchaseOrder::select('id', 'supplier_id', 'due_date', 'status', 'sum_grandtotal')
        ->where('supplier_id', $this->id)
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->whereDate('due_date', '<=', $dateFrom);
      return $purchases->get();
    });

    $amountDue = 0;
    foreach ($purchases as $purchase) {
      $paidAmount = PurchasePayment::where('purchase_order_id', $purchase->id)->whereDate('created_at', '<=', $finalDate)->whereNull('nullified_at')->sum('amount_applied');

      $amountDue += ($purchase->sum_grandtotal - floatval($paidAmount));
    }

    if (Carbon::parse($this->created_at)->diffInDays($finalDate, false) >= 91) {
      $amountDue += $this->getOpeningBalanceAmountDue($finalDate);
    }

    return $amountDue;
  }
}
