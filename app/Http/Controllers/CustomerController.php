<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;

use App\Models\CustomerCCEmail;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewAny', Customer::class);
    $customers = Customer::with(['carboncopyemails'])->orderBy('id', 'desc')->get();
    if ($request->is('api/*')) {
      return $customers;
    } else {
      return view('system.customers.index', [
        'customers' => $customers,
      ]);
    }
  }

  public function store(Request $request)
  {
    $this->authorize('create', Customer::class);
    $request->validate([
      'customer_name' => ['required'],
      'credit_limit' => ['required'],
      'contact_number' => ['required'],
      'location' => ['required'],
      'company' => ['required'],
      'contact_name' => ['required'],
      'opening_balance' => ['required'],
    ]);

    $customer = new Customer();

    $customer->fill([
      'customer_name' => strtoupper($request->customer_name),
      'contact_person_email' => strtoupper($request->contact_email),
      'contact_person_name' => strtoupper($request->contact_name),
      'credit_limit' => $request->credit_limit,
      'opening_balance' => $request->opening_balance,
      'contact_number' => $request->contact_number,
      'location' => strtoupper($request->location),
      'company' => strtoupper($request->company),
      'kra_pin' => $request->kra_pin,
      'company_id' => auth()->user()->company_id
    ]);

    if ($customer->save()) {
      if(count($request->cc_emails) > 0){
        for ($i = 0; $i < count($request->cc_emails); $i++) {
          if (!empty($request->cc_emails[$i])) {
            $customerCCEmail = new CustomerCCEmail();
            $customerCCEmail->fill([
              'customer_id' => $customer->id,
              'email' => strtoupper($request->cc_emails[$i])
            ]);
            $customerCCEmail->save();
          }
        }
      }
      return back()->with('Success', 'Created successfully');
    } else {
      return back()->with('Error', 'Failed to create. Please retry');
    }
  }

  public function update(Request $request, Customer $customer)
  {
    $this->authorize('update', Customer::class);
    $request->validate([
      'customer_name' => ['required'],
      'credit_limit' => ['required'],
      'contact_number' => ['required'],
      'location' => ['required'],
      'company' => ['required'],
      'customer_id' => ['required'],
    ]);

    $customer->fill([
      'customer_name' => strtoupper($request->customer_name),
      'contact_person_email' => strtoupper($request->contact_email),
      'contact_person_name' => strtoupper($request->contact_name),
      'credit_limit' => $request->credit_limit,
      'contact_number' => $request->contact_number,
      'location' => strtoupper($request->location),
      'company' => strtoupper($request->company),
      'kra_pin' => $request->kra_pin,
      'company_id' => auth()->user()->company_id
    ]);

    if ($customer->update()) {
      if($request->cc_email_id){
        for ($i = 0; $i < count($request->cc_email_id); $i++) {
          $customerCCEmail = CustomerCCEmail::find($request->cc_email_id[$i]);
          $customerCCEmail->fill([
            'customer_id' => $customer->id,
            'email' => strtoupper($request->cc_emails[$i])
          ]);
          $customerCCEmail->update();
        }
      }
      if($request->new_cc_email){
        for ($i = 0; $i < count($request->new_cc_email); $i++) {
          if (!empty($request->new_cc_email[$i])) {
            $customerCCEmail = new CustomerCCEmail();
            $customerCCEmail->fill([
              'customer_id' => $customer->id,
              'email' => strtoupper($request->new_cc_email[$i])
            ]);
            $customerCCEmail->save();
          }
        }
      }
      if ($request->cc_email_delete) {
        for ($i = 0; $i < count($request->cc_email_delete); $i++) {
          $customerCCEmail = CustomerCCEmail::find($request->cc_email_delete[$i]);
          $customerCCEmail->delete();
        }
      }
      return back()->with('Success', 'Edited successfully');
    } else {
      return back()->with('Error', 'Failed to edit. Please retry');
    }
  }

  public function destroy(Customer $customer)
  {
    $this->authorize('delete', Customer::class);
    if ($customer->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry');
    }
  }

  public function agingReport(Request $request)
  {
    if (!Gate::allows('accounting')) {
      abort(403);
    }

    $carbonDate = Carbon::now();
    $date = $carbonDate->format('Y-m-d');

    if ($request->date) {
      $carbonDate = Carbon::parse($request->date);
      $date = $carbonDate->format('Y-m-d');
    }

    $invoices = Cache::remember('aging_report_invoices_'.$date, (60 * 30), function () use($date) {
      return Invoice::select('id', 'customer_id', 'created_at')
              ->with([
                'coatingjobs:id,sum_grandtotal,invoice_id',
                'invoicepayments' => function ($query) use($date) {
                  $query->whereDate('created_at', '<=', $date);
                }
              ])
              ->whereNull('cancelled_at')
              ->whereDate('created_at', '<=', $date)
              ->orderBy('customer_id')
              ->get();
    });

    $invoices = Cache::remember('aging_report_invoices_with_balance_'.$date, (60 * 30), function () use($invoices) {
      foreach ($invoices as $invoice) {
        $paymentsTotal = 0;
        $invoiceTotal = 0;
  
        foreach ($invoice->coatingjobs as $coatingjob) {
          $invoiceTotal += $coatingjob->sum_grandtotal;
        }
  
        foreach ($invoice->invoicepayments as $invoicepayment) {
          $paymentsTotal += $invoicepayment->amount_applied;
        }
  
        $invoice->setAttribute('balance',($invoiceTotal - $paymentsTotal));
      }
      return $invoices;
    });

    $customerInvoices = Cache::remember('aging_report_invoices_with_balance_grouped_by_customer', (60 * 30), function () use($invoices) {
      return $this->groupBy($invoices, 'customer_id');
    });

    $customers = collect([]);
    
    foreach ($customerInvoices as $customerID => $invoices) {
      $customer = Customer::select('id', 'customer_name')->where('id', $customerID)->first();

      $customer->setAttribute('current_balance', 0);
      $customer->setAttribute('thirty_day_balance', 0);
      $customer->setAttribute('sixty_day_balance', 0);
      $customer->setAttribute('ninety_day_balance', 0);
      $customer->setAttribute('over_ninety_day_balance', 0);
      foreach ($invoices as $invoice) {
        // determine date difference
        $invoiceCreation = new Carbon($invoice->created_at);
        $dateDifference = $invoiceCreation->diff($carbonDate)->days;

        if($dateDifference < 1){
          $customer->current_balance += $invoice->balance;
        }else if($dateDifference > 0 && $dateDifference < 31){
          $customer->thirty_day_balance += $invoice->balance;
        }else if($dateDifference > 30 && $dateDifference < 61){
          $customer->sixty_day_balance += $invoice->balance;
        }else if($dateDifference > 60 && $dateDifference < 91){
          $customer->ninety_day_balance += $invoice->balance;
        }else if($dateDifference > 90){
          $customer->over_ninety_day_balance += $invoice->balance;          
        }
      }

      $customers->push($customer);
    }

    return view('system.customers.agingreport', [
      'customers' => $customers,
      'date' => $request->date
    ]);
  }

  public function indexStatements(Request $request)
  {
    $customers = Customer::select('id', 'customer_name')->get();
    $selectedCustomers = $request->customers;
    return view('system.customers.statements', [
      'customers' => $customers,
      'selectedCustomers' => $selectedCustomers,
      'from' => $request->from,
      'to' => $request->to,
      'statementDate' => $request->statement_date
    ]);
  }

  public function showStatements(Request $request, Customer $customer)
  {
    $recordsArray = $this->recordsArray($customer, $request->from, $request->to);

    $broughtForward = $customer->getOpeningBalance($request->from);

    return view('system.customers.statements-doc', [
      'customer' => $customer,
      'from' => $request->from,
      'to' => $request->to,
      'statementDate' => $request->statement_date,
      'recordsArray' => $recordsArray,
      'broughtForward' => $broughtForward
    ]);
  }

  private function recordsArray(Customer $customer, $from, $to)
  {
    $recordsArray = array();

    $invoices = Cache::remember('customer_invoices_custom_range_'.$customer->id.'_'. $from .'_'.$to, (60 * 30), function () use($from, $to, $customer) {
      $dateFrom = Carbon::parse($from)->format('Y-m-d');
      $dateTo = Carbon::parse($to)->addDay(1)->format('Y-m-d');
      return Invoice::select('id', 'customer_id', 'invoice_prefix', 'invoice_suffix' ,'created_at')
              ->with([
                'coatingjobs:id,sum_grandtotal,invoice_id'
              ])
              ->whereNull('cancelled_at')
              ->where('customer_id', $customer->id)
              ->whereBetween('created_at', [$dateFrom, $dateTo])
              ->orderBy('customer_id')
              ->get();
    });

    $cacheName = 'customer_invoices_custom_range_with_total_'.$customer->id.'_'. $from .'_'.$to;

    $invoices = Cache::remember($cacheName, (60 * 30), function () use($invoices) {
      foreach ($invoices as $invoice) {
        $invoiceTotal = 0;
  
        foreach ($invoice->coatingjobs as $coatingjob) {
          $invoiceTotal += $coatingjob->sum_grandtotal;
        }
  
        $invoice->setAttribute('total', $invoiceTotal);
      }
      return $invoices;
    });

    foreach ($invoices as $invoice) {
      $columnData = array(
        'type' => 'Invoice',
        'id' => 'INV- #' . $invoice->invoice_prefix . $invoice->invoice_suffix,
        'amount' => $invoice->total,
        'date' => $invoice->created_at,
      );
      array_push($recordsArray, $columnData);
    }

    $cacheName = 'customer_payments_custom_range_' . $customer->id . '_' . $to . '_' . $from;

    $payments = Cache::remember($cacheName, (60 * 3), function () use ($to, $from, $customer) {
      $dateFrom = Carbon::parse($from)->format('Y-m-d');
      $dateTo = Carbon::parse($to)->addDay(1)->format('Y-m-d');
      $payments = Payment::select('transaction_ref', 'payment_date')
                  ->where('customer_id', $customer->id)
                  ->whereBetween('created_at', [$dateFrom, $dateTo]);
      return $payments->get();
    });

    foreach ($payments as $payment) {
      $columnData = array(
        'type' => 'Payment',
        'id' => 'PYMT- ' . $payment->transaction_ref,
        'amount' => ($payment->paid_amount * -1),
        'date' => $payment->payment_date,
      );
      array_push($recordsArray, $columnData);

      if ($payment->nullified_at) {
        $columnData = array(
          'type' => 'Nullified',
          'id' => 'BOUNCED PYMT- ' . $payment->transaction_ref,
          'amount' => $payment->paid_amount,
          'date' => $payment->payment_date,
        );
        array_push($recordsArray, $columnData);
      }
    }

    usort($recordsArray, function ($item1, $item2) {
      return $item1['date'] <=> $item2['date'];
    });

    return $recordsArray;
  }

  public function getCustomerInvoices(Request $request, Customer $customer)
  {

    $invoices = Cache::remember('customer_invoices_' . $customer->id, (60 * 60 * 1), function () use ($customer) {
      return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'created_at', 'amount_due')
        ->where('customer_id', $customer->id)
        ->orderBy('id', 'asc')
        ->get();
    });

    $html = view('system.customers.misc.searchlist', [
      'invoices' => $invoices
    ])->render();

    return $html;
  }

  public function getCustomerInvoicesOptions(Request $request, Customer $customer)
  {

    $invoices = Cache::remember('customer_invoices_' . $customer->id, (60 * 60 * 1), function () use ($customer) {
      return Invoice::select('id', 'invoice_prefix', 'invoice_suffix', 'created_at')
        ->where('customer_id', $customer->id)
        ->orderBy('id', 'asc')
        ->get()
        ->append('amount_due');
    });

    $html = view('system.customers.misc.options', [
      'invoices' => $invoices
    ])->render();

    return $html;
  }
}
