<?php

namespace App\Http\Controllers;

use App\Enums\PaymentModesEnum;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Cache::remember('payments', (60 * 2), function () {
            return Payment::select('id', 'payment_mode', 'transaction_ref', 'sum_invoice_payments', 'payment_date', 'nullified_at', 'customer_id')
                ->with(['customer:id,customer_name'])
                ->orderBy('id', 'desc')
                ->get();
        });
        return view('system.payments.index', [
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
        $customers = Customer::select('id', 'customer_name')->get();
        $paymentTypes = PaymentModesEnum::cases();
        return view('system.payments.create', [
            'customers' => $customers,
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
            'customer_id' => ['required'],
            'payment_mode' => ['required'],
            'payment_date' => ['required'],
        ]);

        if (count($request->invoice_id) < 1) {
            return back()->with('Error', 'No item to settle');
        }

        $payment = new Payment();

        $payment->fill([
            'customer_id' => $request->customer_id,
            'payment_mode' => $request->payment_mode,
            'transaction_ref' => $request->transaction_ref,
            'payment_date' => Carbon::createFromFormat('Y-m-d', $request->payment_date),
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->user()->id
        ]);

        if ($payment->save()) {
            for ($i = 0; $i < count($request->invoice_id); $i++) {
                $invoicePayment = new InvoicePayment();
                $invoicePayment->fill([
                    'payment_id' => $payment->id,
                    'invoice_id' => $request->invoice_id[$i],
                    'amount_applied' => $request->amount_paid[$i]
                ]);
                $invoicePayment->save();
            }

            $payment->updateAmounts();

            for ($i = 0; $i < count($request->invoice_id); $i++) {
                $invoice = Invoice::find($request->invoice_id[$i]);
                $invoice->calculateAmountDue();
            }

            Cache::forget('payments');
            return redirect('/payments')->with('Success', "Created Successfully");
        } else {
            return back()->with('Error', 'Failed to create. Please retry');
        }
    }

    public function show(Payment $payment)
    {
    }

    public function edit(Payment $payment)
    {
        $customer = Cache::remember('customer_invoices_payment_' . $payment->customer_id, (60 * 2), function () use ($payment) {
            return Customer::where('id', $payment->customer_id)
                ->with([
                    'invoices' => function ($query) use ($payment) {
                        $paidInvoices = $payment->invoicepayments()->pluck('invoice_id')->toArray();
                        $query->whereNotIn('id', $paidInvoices);
                    }
                ])
                ->first();
        });

        $paymentTypes = PaymentModesEnum::cases();

        return view('system.payments.edit', [
            'customer' => $customer,
            'paymentTypes' => $paymentTypes,
            'payment' => $payment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'customer_id' => ['required'],
            'payment_mode' => ['required'],
            'payment_date' => ['required'],
        ]);

        $payment->fill([
            'customer_id' => $request->customer_id,
            'payment_mode' => $request->payment_mode,
            'transaction_ref' => $request->transaction_ref,
            'payment_date' => Carbon::createFromFormat('Y-m-d', $request->payment_date),
            'company_id' => auth()->user()->company_id,
        ]);

        if ($payment->update()) {
            if ($request->invoice_payment_id) {
                for ($i = 0; $i < count($request->invoice_payment_id); $i++) {
                    $invoicePayment = InvoicePayment::find($request->invoice_payment_id[$i]);
                    $invoicePayment->fill([
                        'payment_id' => $payment->id,
                        'invoice_id' => $request->invoice_id[$i],
                        'amount_applied' => $request->amount_paid[$i]
                    ]);
                    $invoicePayment->save();
                }
            }
            if ($request->new_invoice_id) {
                for ($i = 0; $i < count($request->new_invoice_id); $i++) {
                    $invoicePayment = new InvoicePayment();
                    $invoicePayment->fill([
                        'payment_id' => $payment->id,
                        'invoice_id' => $request->new_invoice_id[$i],
                        'amount_applied' => $request->new_amount_paid[$i]
                    ]);
                    $invoicePayment->save();
                }
            }
            if ($request->remove_invoice_payment_id) {
                for ($i = 0; $i < count($request->remove_invoice_payment_id); $i++) {
                    $invoicePayment = InvoicePayment::find($request->remove_invoice_payment_id[$i]);
                    $invoicePayment->delete();

                    $invoice = Invoice::find($invoicePayment->invoice_id);
                    $invoice->calculateAmountDue();
                }
            }
            $payment->updateAmounts();

            $payment->refresh();

            foreach ($payment->invoicepayments as $invoicePayment) {
                $invoice = Invoice::find($invoicePayment->invoice_id);
                $invoice->calculateAmountDue();
            }

            Cache::forget('payments');
            return redirect('/payments')->with('Success', "Edited Successfully");
        } else {
            return back()->with('Error', 'Failed to edit. Please retry');
        }
    }

    public function nullify(Request $request, Payment $payment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $payment->fill([
            'nullified_at' => Carbon::now()
        ]);

        $invoicePayments = $payment->invoicepayments;

        if ($payment->update()) {

            foreach ($invoicePayments as $invoicePayment) {
                $invoicePayment->update([
                    'nullified_at' => Carbon::now()
                ]);
                $invoice = Invoice::find($invoicePayment->invoice_id);
                $invoice->calculateAmountDue();
            }

            Cache::forget('payments');

            return back()->with('Success', 'Successfully nullified');
        } else {
            return back()->with('Error', 'Failed. Please retry');
        }
    }

    public function destroy(Payment $payment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $invoicePayments = $payment->invoicepayments;

        if ($payment->delete()) {

            foreach ($invoicePayments as $invoicePayment) {
                $invoicePayment->delete();
                $invoice = Invoice::find($invoicePayment->invoice_id);
                $invoice->calculateAmountDue();
            }

            Cache::forget('payments');

            return back()->with('Success', 'Successfully deleted');
        } else {
            return back()->with('Error', 'Failed. Please retry');
        }
    }

    public function nullifyForm(Payment $payment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $html = view('system.payments.misc.nullify-form', [
            'payment' => $payment,
        ])->render();

        return $html;
    }

    public function deleteForm(Payment $payment)
    {
        if (!Gate::allows('accounting')) {
            abort(403);
        }

        $html = view('system.payments.misc.delete-form', [
            'payment' => $payment,
        ])->render();

        return $html;
    }
}
