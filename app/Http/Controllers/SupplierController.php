<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatusEnum;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class SupplierController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('viewAny', Supplier::class);
    $suppliers = Supplier::orderBy('id', 'desc')->get();
    if ($request->is('api/*')) {
      return $suppliers;
    } else {
      return view('system.suppliers.index', [
        'suppliers' => $suppliers,
      ]);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->authorize('create', Supplier::class);
    $request->validate([
      'supplier_name' => ['required'],
      'supplier_email' => ['required'],
      'supplier_mobile' => ['required'],
      'opening_balance' => ['required'],
      'company_location' => ['required'],
      'company_pin' => ['required'],
      'company_box' => ['required'],
    ]);

    $supplier = new Supplier();

    $supplier->fill([
      'supplier_name' => strtoupper($request->supplier_name),
      'supplier_email' => strtoupper($request->supplier_email),
      'supplier_mobile' => strtoupper($request->supplier_mobile),
      'company_location' => strtoupper($request->company_location),
      'company_pin' => strtoupper($request->company_pin),
      'opening_balance' => $request->opening_balance,
      'company_box' => strtoupper($request->company_box),
      'company_id' => auth()->user()->company_id
    ]);

    if ($supplier->save()) {
      if ($request->is('api/*')) {
        return $supplier;
      } else {
        return back()->with('Success', 'New supplier has been created');
      }
    } else {
      return back()->with('Error', 'Failed to create new supplier. Please retry');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Supplier  $supplier
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Supplier $supplier)
  {
    $this->authorize('update', Supplier::class);
    $request->validate([
      'supplier_name' => ['required'],
      'supplier_email' => ['required'],
      'supplier_mobile' => ['required'],
      'company_location' => ['required'],
      'company_pin' => ['required'],
      'company_box' => ['required'],
      'opening_balance' => ['required']
    ]);

    $supplier->fill([
      'supplier_name' => strtoupper($request->supplier_name),
      'supplier_email' => strtoupper($request->supplier_email),
      'supplier_mobile' => strtoupper($request->supplier_mobile),
      'company_location' => strtoupper($request->company_location),
      'opening_balance' => $request->opening_balance,
      'company_pin' => strtoupper($request->company_pin),
      'company_box' => strtoupper($request->company_box),
      'company_id' => auth()->user()->company_id
    ]);

    if ($supplier->update()) {
      if ($request->is('api/*')) {
        return $supplier;
      } else {
        return back()->with('Success', 'Supplier has been edited');
      }
    } else {
      return back()->with('Error', 'Failed to edit the supplier. Please retry');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Supplier  $supplier
   * @return \Illuminate\Http\Response
   */
  public function destroy(Supplier $supplier)
  {
    $this->authorize('delete', Supplier::class);
    if ($supplier->delete()) {
      return back()->with('Success', 'Supplier has been deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete supplier. Please retry');
    }
  }

  public function viewSupplierTransactions()
  {
    $suppliers  = Supplier::where('company_id', session()->get('auth_company_uid'))->with('purchaseorders')->with('creditnotes')->with('bills')->get();

    $recordsArray = array();

    $grandTotal = 0;

    // get payments
    foreach ($suppliers as $supplier) {
      foreach ($supplier->bills as $singlePaymentInfo) {
        $columnData = array(
          'type' => 'Payment',
          'id' =>  $singlePaymentInfo->transaction_ref,
          'amount' => $singlePaymentInfo->amount,
          'referenced' => $singlePaymentInfo->referenced_invoice,
          'date' => $singlePaymentInfo->date_created,
          'supplier_id' => $supplier->id,
          'supplier_name' => $supplier->supplier_name,
        );
        array_push($recordsArray, $columnData);
        $grandTotal = $grandTotal - $columnData['amount'];
      }

      // get credit notes
      foreach ($supplier->creditnotes as $singleCreditNote) {

        $columnData = array(
          'type' => 'Credit Note',
          'id' => $singleCreditNote->credit_prefix . $singleCreditNote->credit_suffix,
          'amount' => $singleCreditNote->grand_total,
          'referenced' => $singleCreditNote->invoice_ref,
          'date' => $singleCreditNote->date_created,
          'supplier_id' => $supplier->id,
          'supplier_name' => $supplier->supplier_name,
        );
        array_push($recordsArray, $columnData);
        $grandTotal = $grandTotal - $columnData['amount'];
      }

      // get invoices
      foreach ($supplier->purchaseorders as $singleInvoiceInfo) {
        $columnData = array(
          'type' => 'Purchase Order',
          'id' => $singleInvoiceInfo->lpo_prefix . $singleInvoiceInfo->lpo_suffix,
          'amount' => $singleInvoiceInfo->grand_total,
          'date' => $singleInvoiceInfo->date_created,
          'supplier_id' => $supplier->id,
          'supplier_name' => $supplier->supplier_name,
        );
        array_push($recordsArray, $columnData);

        $grandTotal = $grandTotal + floatval($columnData['amount']);
      }

      usort($recordsArray, function ($item1, $item2) {
        return $item1['date'] <=> $item2['date'];
      });
    }

    return view('system.accounts.supplier-transactions', [
      'suppliers' => $suppliers,
      'recordsArray' => $recordsArray,
      'grandTotal' => $grandTotal,
    ]);
  }

  public function stockInRecords(Request $request)
  {
    $supplierTransactions = Supplier::with(['purchaseorders', 'bills', 'creditnotes'])->where('company_id', session()->get('auth_company_uid'))->get();

    $time = time();
    if (isset($request->date)) {
      $time = strtotime($request->date);
    }

    return view('system.expenses.payable', [
      'supplierTransactions' => $supplierTransactions,
      'time' => $time,
    ]);
  }

  public function getSupplierPurchasesOptions(Request $request, Supplier $supplier)
  {
    $purchases = Cache::remember('supplier_purchases_' . $supplier->id, (60 * 60 * 1), function () use ($supplier) {
      return PurchaseOrder::select('id', 'lpo_prefix', 'lpo_suffix', 'created_at')
        ->where('supplier_id', $supplier->id)
        ->whereNotNull('lpo_suffix')
        ->orderBy('id', 'asc')
        ->get()
        ->append('amount_due');
    });

    $html = view('system.suppliers.misc.options', [
      'purchases' => $purchases
    ])->render();

    return $html;
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

    $purchases = Cache::remember('aging_report_purchase_orders_' . $date, (60 * 30), function () use ($date) {
      return PurchaseOrder::select('id', 'supplier_id', 'created_at', 'due_date', 'sum_grandtotal')
        ->with([
          'purchasepayments' => function ($query) use ($date) {
            $query->whereDate('created_at', '<=', $date);
          }
        ])
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->whereDate('due_date', '<=', $date)
        ->orderBy('due_date')
        ->orderBy('supplier_id')
        ->get();
    });

    $purchases = Cache::remember('aging_report_purchases_with_balance_' . $date, (60 * 30), function () use ($purchases) {
      $paymentsTotal = 0;
      foreach ($purchases as $purchase) {

        $purchaseTotal = $purchase->sum_grandtotal;

        foreach ($purchase->purchasepayments as $purchasepayment) {
          $paymentsTotal += $purchasepayment->amount_applied;
        }

        $purchase->setAttribute('balance', ($purchaseTotal - $paymentsTotal));
      }
      return $purchases;
    });

    $supplierPurchases = Cache::remember('aging_report_purchases_with_balance_grouped_by_supplier', (60 * 30), function () use ($purchases) {
      return $this->groupBy($purchases, 'supplier_id');
    });

    $suppliers = collect([]);

    foreach ($supplierPurchases as $supplierID => $purchases) {
      $supplier = Supplier::select('id', 'supplier_name')->where('id', $supplierID)->first();

      $supplier->setAttribute('current_balance', 0);
      $supplier->setAttribute('thirty_day_balance', 0);
      $supplier->setAttribute('sixty_day_balance', 0);
      $supplier->setAttribute('ninety_day_balance', 0);
      $supplier->setAttribute('over_ninety_day_balance', 0);
      foreach ($purchases as $purchase) {
        // determine date difference
        $purchaseDueDate = new Carbon($purchase->due_date);
        $dateDifference = $purchaseDueDate->diff($carbonDate)->days;
        
        if ($dateDifference < 1) {
          $supplier->current_balance += $purchase->balance;
        } else if ($dateDifference > 0 && $dateDifference < 31) {
          $supplier->thirty_day_balance += $purchase->balance;
        } else if ($dateDifference > 30 && $dateDifference < 61) {
          $supplier->sixty_day_balance += $purchase->balance;
        } else if ($dateDifference > 60 && $dateDifference < 91) {
          $supplier->ninety_day_balance += $purchase->balance;
        } else if ($dateDifference > 90) {
          $supplier->over_ninety_day_balance += $purchase->balance;
        }
      }

      $suppliers->push($supplier);
    }

    return view('system.suppliers.agingreport', [
      'suppliers' => $suppliers,
      'date' => $request->date
    ]);
  }

  public function getSupplierPurchases(Request $request, Supplier $supplier)
  {
    $purchases = Cache::remember('supplier_purchases_' . $supplier->id, (60 * 60 * 1), function () use ($supplier) {
      return PurchaseOrder::select('id', 'lpo_prefix', 'lpo_suffix', 'created_at', 'amount_due')
        ->where('supplier_id', $supplier->id)
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->where('amount_due', '>', 0)
        ->orderBy('id', 'asc')
        ->get();
    });

    $html = view('system.suppliers.misc.searchlist', [
      'purchases' => $purchases
    ])->render();

    return $html;
  }

  public function indexStatements(Request $request)
  {
    $suppliers = Supplier::select('id', 'supplier_name')->get();
    $selectedSuppliers = $request->suppliers;
    return view('system.suppliers.statements', [
      'suppliers' => $suppliers,
      'selectedSuppliers' => $selectedSuppliers,
      'from' => $request->from,
      'to' => $request->to,
      'statementDate' => $request->statement_date
    ]);
  }

  public function showStatements(Request $request, Supplier $supplier)
  {
    $recordsArray = $this->recordsArray($supplier, $request->from, $request->to);

    $broughtForward = $supplier->getOpeningBalance($request->from);

    return view('system.suppliers.statements-doc', [
      'supplier' => $supplier,
      'from' => $request->from,
      'to' => $request->to,
      'statementDate' => $request->statement_date,
      'recordsArray' => $recordsArray,
      'broughtForward' => $broughtForward
    ]);
  }

  private function recordsArray(Supplier $supplier, $from, $to)
  {
    $recordsArray = array();

    $purchases = Cache::remember('supplier_purchases_custom_range_' . $supplier->id . '_' . $from . '_' . $to, (60 * 30), function () use ($from, $to, $supplier) {
      $dateFrom = Carbon::parse($from)->format('Y-m-d');
      $dateTo = Carbon::parse($to)->addDay(1)->format('Y-m-d');
      return PurchaseOrder::select('id', 'supplier_id', 'lpo_prefix', 'lpo_suffix', 'sum_grandtotal', 'created_at', 'due_date')
        ->where('supplier_id', $supplier->id)
        ->where('status', PurchaseOrderStatusEnum::CLOSED->value)
        ->where('amount_due', '>', 0)
        ->whereBetween('due_date', [$dateFrom, $dateTo])
        ->orderBy('supplier_id')
        ->get();
    });

    $cacheName = 'supplier_purchases_custom_range_with_total_' . $supplier->id . '_' . $from . '_' . $to;

    $purchases = Cache::remember($cacheName, (60 * 30), function () use ($purchases) {
      foreach ($purchases as $purchase) {
        $purchase->setAttribute('total', $purchase->sum_grandtotal);
      }
      return $purchases;
    });

    foreach ($purchases as $purchase) {
      $columnData = array(
        'type' => 'Purchase',
        'id' => 'Purchase: ' . $purchase->lpo_prefix . $purchase->lpo_suffix,
        'amount' => $purchase->total,
        'date' => $purchase->created_at,
      );
      array_push($recordsArray, $columnData);
    }

    $cacheName = 'supplier_payments_custom_range_' . $supplier->id . '_' . $to . '_' . $from;

    $payments = Cache::remember($cacheName, (60 * 3), function () use ($to, $from, $supplier) {
      $dateFrom = Carbon::parse($from)->format('Y-m-d');
      $dateTo = Carbon::parse($to)->addDay(1)->format('Y-m-d');
      $payments = SupplierPayment::select('transaction_ref', 'payment_date', 'sum_purchase_payments', 'nullified_at')
        ->where('supplier_id', $supplier->id)
        ->whereBetween('payment_date', [$dateFrom, $dateTo]);
      return $payments->get();
    });

    foreach ($payments as $payment) {
      $columnData = array(
        'type' => 'Payment',
        'id' => 'PYMT- ' . $payment->transaction_ref,
        'amount' => ($payment->sum_purchase_payments * -1),
        'date' => $payment->payment_date,
      );
      array_push($recordsArray, $columnData);

      if ($payment->nullified_at) {
        $columnData = array(
          'type' => 'Nullified',
          'id' => 'BOUNCED PYMT- ' . $payment->transaction_ref,
          'amount' => $payment->sum_purchase_payments,
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
}
