<?php

namespace App\Http\Controllers;

use App\Enums\PaymentModesEnum;
use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Cache;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Cache::remember('supplier_bill_payments', (60 * 2), function () {
            return SupplierPayment::select('id', 'payment_mode', 'transaction_ref', 'sum_purchase_payments', 'payment_date', 'nullified_at', 'supplier_id')
                ->with(['supplier:id,supplier_name'])
                ->orderBy('id', 'desc')
                ->get();
        });

        return view('system.supplier-payments.index', [
            'payments' => $payments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::select('id', 'supplier_name')->get();
        $paymentTypes = PaymentModesEnum::cases();
        return view('system.supplier-payments.create', [
            'suppliers' => $suppliers,
            'paymentTypes' => $paymentTypes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'payment_mode' => ['required'],
            'payment_date' => ['required'],
            'transaction_ref' => ['required']
        ]);

        if (count($request->purchase_order_id) < 1) {
            return back()->with('Error', 'No item to settle');
        }

        $payment = new SupplierPayment();

        $payment->fill([
            'supplier_id' => $request->supplier_id,
            'payment_mode' => $request->payment_mode,
            'transaction_ref' => $request->transaction_ref,
            'payment_date' => Carbon::createFromFormat('Y-m-d', $request->payment_date),
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->user()->id
        ]);

        if ($payment->save()) {
            for ($i = 0; $i < count($request->purchase_order_id); $i++) {
                $purchasePayment = new PurchasePayment();
                $purchasePayment->fill([
                    'supplier_payment_id' => $payment->id,
                    'purchase_order_id' => $request->purchase_order_id[$i],
                    'amount_applied' => $request->amount_paid[$i]
                ]);
                $purchasePayment->save();
            }
            $payment->updateAmounts();
            for ($i = 0; $i < count($request->purchase_order_id); $i++) {
                $purchaseOrder = PurchaseOrder::find($request->purchase_order_id[$i]);
                $purchaseOrder->calculateAmountDue();
            }
            Cache::forget('supplier_bill_payments');
            return redirect('/supplier-payments')->with('Success', "Created Successfully");
        } else {
            return back()->with('Error', 'Failed to create. Please retry');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierPayment $supplierPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        $supplier = Cache::remember('supplier_purchases_payment_' . $supplierPayment->supplier_id, (60 * 2), function () use ($supplierPayment) {
            return Supplier::where('id', $supplierPayment->supplier_id)
                ->with([
                    'purchaseorders' => function ($query) use ($supplierPayment) {
                        $paidPurchase = $supplierPayment->purchasepayments()->pluck('purchase_order_id')->toArray();
                        $query->whereNotIn('id', $paidPurchase);
                    }
                ])
                ->first();
        });

        $paymentTypes = PaymentModesEnum::cases();

        return view('system.supplier-payments.edit', [
            'supplier' => $supplier,
            'paymentTypes' => $paymentTypes,
            'payment' => $supplierPayment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'payment_mode' => ['required'],
            'payment_date' => ['required'],
        ]);

        $supplierPayment->fill([
            'supplier_id' => $request->supplier_id,
            'payment_mode' => $request->payment_mode,
            'transaction_ref' => $request->transaction_ref,
            'payment_date' => Carbon::createFromFormat('Y-m-d', $request->payment_date),
            'company_id' => auth()->user()->company_id,
        ]);

        if ($supplierPayment->update()) {
            if ($request->purchase_payment_id) {
                for ($i = 0; $i < count($request->purchase_payment_id); $i++) {
                    $purchasePayment = PurchasePayment::find($request->purchase_payment_id[$i]);
                    $purchasePayment->fill([
                        'payment_id' => $supplierPayment->id,
                        'purchase_order_id' => $request->purchase_order_id[$i],
                        'amount_applied' => $request->amount_paid[$i]
                    ]);
                    $purchasePayment->save();
                }
            }
            if ($request->new_purchase_order_id) {
                for ($i = 0; $i < count($request->new_purchase_order_id); $i++) {
                    $purchasePayment = new PurchasePayment();
                    $purchasePayment->fill([
                        'supplier_payment_id' => $supplierPayment->id,
                        'purchase_order_id' => $request->new_purchase_order_id[$i],
                        'amount_applied' => $request->new_amount_paid[$i]
                    ]);
                    $purchasePayment->save();
                }
            }
            if ($request->remove_purchase_payment_id) {
                for ($i = 0; $i < count($request->remove_purchase_payment_id); $i++) {
                    $purchasePayment = PurchasePayment::find($request->remove_purchase_payment_id[$i]);
                    $purchasePayment->delete();

                    $purchaseOrder = PurchaseOrder::find($purchasePayment->purchase_order_id);
                    $purchaseOrder->calculateAmountDue();
                }
            }
            $supplierPayment->updateAmounts();

            $supplierPayment->refresh();

            foreach ($supplierPayment->purchasepayments as $purchasePayment) {
                $purchaseOrder = PurchaseOrder::find($purchasePayment->purchase_order_id);
                $purchaseOrder->calculateAmountDue();
            }

            Cache::forget('supplier_bill_payments');
            return redirect('/supplier-payments')->with('Success', "Edited Successfully");
        } else {
            return back()->with('Error', 'Failed to edit. Please retry');
        }
        die();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierPayment  $supplierPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierPayment $supplierPayment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $purchasePayments = $supplierPayment->purchasepayments;
        if ($supplierPayment->delete()) {
            foreach ($purchasePayments as $purchasePayment) {
                $purchasePayment->delete();
                $purchaseOrder = PurchaseOrder::find($purchasePayment->purchase_order_id);
                $purchaseOrder->calculateAmountDue();
            }

            Cache::forget('supplier_bill_payments');
            return back()->with('Success', 'Successfully deleted');
        } else {
            return back()->with('Error', 'Failed. Please retry');
        }
    }

    public function nullify(Request $request, SupplierPayment $supplierPayment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $supplierPayment->fill([
            'nullified_at' => Carbon::now()
        ]);

        $purchasePayments = $supplierPayment->purchasepayments;

        if ($supplierPayment->update()) {

            foreach ($purchasePayments as $purchasePayment) {
                $purchasePayment->update([
                    'nullified_at' => Carbon::now()
                ]);
                $purchaseOrder = PurchaseOrder::find($purchasePayment->purchase_order_id);
                $purchaseOrder->calculateAmountDue();
            }

            Cache::forget('supplier_bill_payments');
            return back()->with('Success', 'Successfully nullified');
        } else {
            return back()->with('Error', 'Failed. Please retry');
        }
    }

    public function nullifyForm(SupplierPayment $supplierPayment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $html = view('system.supplier-payments.misc.nullify-form', [
            'payment' => $supplierPayment,
        ])->render();

        return $html;
    }

    public function deleteForm(SupplierPayment $supplierPayment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $html = view('system.supplier-payments.misc.delete-form', [
            'payment' => $supplierPayment,
        ])->render();

        return $html;
    }
}
