<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Customer extends Model
{
  use HasFactory;
  use Traits\ModelTable;
  use Traits\CompanyFilter;
  // use Traits\Syncer;

  protected $fillable = [
    'customer_name',
    'contact_person_email',
    'contact_person_name',
    'credit_limit',
    'opening_balance',
    'contact_number',
    'location',
    'company',
    'kra_pin',
    'company_id'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function invoices()
  {
    return $this->hasMany(Invoice::class);
  }

  public function cashsales()
  {
    return $this->hasMany(CashSale::class);
  }

  public function payments()
  {
    return $this->hasMany(Payment::class);
  }

  public function carboncopyemails()
  {
    return $this->hasMany(CustomerCCEmail::class);
  }

  public function singleDayInvoicesAmountDue($date)
  {
    $carbonDateFormat = Carbon::now()->format('Y-m-d');
    if ($date) {
      $carbonDateFormat = Carbon::parse($date)->format('Y-m-d');
    }

    $invoiceResult = Cache::remember('single_day_invoices_' . $this->id . '_' . $carbonDateFormat, (60 * 3), function () use ($carbonDateFormat) {
      $invoices = Invoice::select('id', 'customer_id', 'amount_due')
        ->where('customer_id', $this->id)
        ->whereDate('created_at', $carbonDateFormat)
        ->whereNull('cancelled_at');
      return $invoices->get();
    });

    $amountDue = $invoiceResult->sum('amount_due');

    if (Carbon::parse($this->created_at)->diffInDays($carbonDateFormat) == 0) {
      $amountDue += $this->getOpeningBalanceAmountDue($carbonDateFormat);
    }

    return $amountDue;
  }

  public function dateRangePeriodInvoicesAmountDue($date, $from, $to)
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

    $cacheName = 'period_invoices_' . $this->id . '_' . $dateTo . '_' . $dateFrom . '_' . $finalDate;

    $invoiceResult = Cache::remember($cacheName, (60 * 3), function () use ($dateTo, $dateFrom) {
      $invoices = Invoice::select('id', 'customer_id', 'amount_due')
        ->where('customer_id', $this->id)
        ->whereBetween('created_at', [$dateTo, $dateFrom])
        ->whereNull('cancelled_at');

      return $invoices->get();
    });

    $amountDue = $invoiceResult->sum('amount_due');

    if (Carbon::parse($this->created_at)->diffInDays($finalDate, false) >= $from && Carbon::parse($this->created_at)->diffInDays($finalDate) < $to) {
      $amountDue += $this->getOpeningBalanceAmountDue($finalDate);
    }

    return $amountDue;
  }

  public function singlePeriodOverInvoicesAmountDue($date, $days)
  {
    $finalDate = Carbon::now()->format('Y-m-d');
    $dateFrom = Carbon::now()->subDays($days)->format('Y-m-d');
    if ($date) {
      $dateFrom = Carbon::parse($date)->subDays($days)->format('Y-m-d');
      $finalDate = Carbon::parse($date)->format('Y-m-d');
    }

    $invoices = Cache::remember('single_day_over_invoices_' . $this->id . '_' . $dateFrom, (60 * 3), function () use ($dateFrom) {
      $invoices = Invoice::select('id', 'customer_id', 'amount_due')
        ->where('customer_id', $this->id)
        ->whereDate('created_at', '<=', $dateFrom)
        ->whereNull('cancelled_at');
      return $invoices->get();
    });

    $amountDue = $invoices->sum('amount_due');

    if (Carbon::parse($this->created_at)->diffInDays($finalDate, false) >= 91) {
      $amountDue += $this->getOpeningBalanceAmountDue($finalDate);
    }

    return $amountDue;
  }

  public function getOpeningBalance($toDate)
  {
    $dateTo = Carbon::now()->format('Y-m-d');
    if ($toDate) {
      $dateTo = Carbon::parse($toDate)->format('Y-m-d');
    }

    $customerOpeningBalanceAmountDue = $this->getOpeningBalanceAmountDue($dateTo);

    $invoicesOpeningBalance = $this->getInvoicesAmountDue($dateTo);

    return round(($customerOpeningBalanceAmountDue + $invoicesOpeningBalance), 2);
  }

  private function getOpeningBalanceAmountDue($dateTo)
  {
    $cacheName = 'period_invoice_payments_' . $dateTo;

    if (Carbon::parse($this->created_at)->diffInDays($dateTo) == 0) {
      $payments = Cache::remember($cacheName, (60 * 3), function () use ($dateTo) {
        return Payment::where('customer_id' ,$this->id)
          ->with([
          'invoicepayments' => function ($query) use ($dateTo) {
            $query->whereNull('invoice_id')->whereNull('nullified_at')->whereDate('created_at', '<=', $dateTo);
          }
        ])->get()->toArray();
      });

      $paidAmount = array_reduce($payments, function ($accumulator, $payment) {
        $amount = 0;
        foreach ($payment['invoicepayments'] as $invoicePayment) {
          $amount += $invoicePayment['amount_applied'];
        }
        return $accumulator += $amount;
      }, 0);

      return floatval($this->opening_balance) - $paidAmount;
    }
    return 0;
  }

  private function getInvoicesAmountDue($dateTo)
  {
    $cacheName = 'invoices_amount_due'. $this->id .'_'. $dateTo;

    $invoices = Cache::remember($cacheName, (60 * 30), function () use($dateTo) {
      return Invoice::select('id', 'customer_id', 'created_at', 'amount_due')
              ->whereNull('cancelled_at')
              ->whereDate('created_at', '<=', $dateTo)
              ->orderBy('customer_id')
              ->get();
    });

    $amountDue = $invoices->sum('amount_due');
    
    return $amountDue;
  }

  public function getInvoicesCustomDateRange($from, $to)
  {
    $dateFrom = Carbon::parse($from)->format('Y-m-d');
    $dateTo = Carbon::parse($to)->addDay(1)->format('Y-m-d');

    $cacheName = 'custom_range_invoices_' . $this->id . '_' .  $dateTo . '_' . $dateFrom;

    $invoices = Cache::remember($cacheName, (60 * 3), function () use ($dateTo, $dateFrom) {
      $invoices = Invoice::select('invoice_prefix', 'invoice_suffix', 'created_at')->whereBetween('created_at', [$dateFrom, $dateTo])->whereNull('cancelled_at');

      return $invoices->get();
    });

    return $invoices;
  }

  public function getPaymentsCustomDateRange($from, $to)
  {
    $dateFrom = Carbon::parse($from)->format('Y-m-d');
    $dateTo = Carbon::parse($to)->addDay(1)->format('Y-m-d');


    $cacheName = 'custom_range_payments_' . $this->id . '_' . $dateTo . '_' . $dateFrom;

    $payments = Cache::remember($cacheName, (60 * 3), function () use ($dateTo, $dateFrom) {
      $payments = Payment::select('transaction_ref', 'payment_date')->whereBetween('created_at', [$dateFrom, $dateTo]);

      return $payments->get();
    });

    return $payments;
  }
}
