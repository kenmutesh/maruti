@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Accounts Customers',
'select2' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />

<body class="theme-blue">
  @include('universal-layout.spinner')

  @include('universal-layout.accounts-sidemenu',
  [
  'slug' => '/accounts'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">
          <div class="content">

            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title p-0 m-0">Edit Payment</h4>
                </div>
                <div class="card-body p-0">
                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('payments.update', $payment->id) }}">
                    @csrf
                    @method("PUT")
                    <div class="d-flex flex-column">

                      <div class="row">
                        <div class="col-sm-5 position-relative mr-5">
                          <div class="form-group">
                            <label>Customer</label>
                            <select class="form-control ms searchable-select customer-select" data-live-search="true" data-style="rounded-sm text-white m-0" required name="customer_id" onchange="filterInvoices(this)">
                              <option disabled selected>Choose a customer</option>
                              <option value="{{ $customer->id }}" selected data-tokens="{{ $customer->customer_name }}">
                                {{ $customer->customer_name }} (CURRENT)
                              </option>
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group row align-items-center">
                            <label>Payment Type</label>
                            <select name="payment_mode" id="" class="form-control searchable-select type-dropdown ms rounded-sm border-dark">
                              <option disabled selected>Choose payment type</option>
                              @foreach($paymentTypes as $paymentType)
                              @if($payment->payment_mode == $paymentType)
                              <option value="{{ $paymentType->value }}" selected>
                                {{ $paymentType->humanreadablestring() }} (CURRENT)
                              </option>
                              @else
                              <option value="{{ $paymentType->value }}">
                                {{ $paymentType->humanreadablestring() }}
                              </option>
                              @endif
                              @endforeach
                            </select>

                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>Amount</label>
                            <input type="number" value="{{ $payment->sum_invoice_payments }}" class="form-control p-1 border-dark rounded-sm" oninput="calculateInvoiceAmounts(this)" required name="amount">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>Code/References</label>
                            <input type="text" value="{{ $payment->transaction_ref }}" class="form-control p-1 border-dark rounded-sm" name="transaction_ref">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>Date</label>
                            <input type="date" required value="{{ date('Y-m-d', strtotime($payment->payment_date)) }}" class="form-control p-1 border-dark rounded-sm" name="payment_date">
                          </div>
                        </div>

                        <div class="table-responsive mt-2">
                          <table class="table table-bordered text-center">
                            <thead>
                              <th class="p-0 border-top"></th>
                              <th class="p-0 border-top">Invoice Date</th>
                              <th class="p-0 border-top">Invoice No.</th>
                              <th class="p-0 border-top">Amount Due</th>
                              <th class="p-0 border-top">Paid Amount</th>
                              <th class="p-0 border-top">Remove Payment</th>
                            </thead>
                            <tbody>
                              @forelse($payment->invoicepayments as $invoicepayment)
                              <tr attr-customer-id="{{ $invoicepayment->invoice->customer->id }}" class="invoice-item">
                                <td class="text-center p-0">
                                  <input type="hidden" name="invoice_payment_id[]" value="{{ $invoicepayment->id }}">
                                  <label class="form-check-label">
                                    <input checked type="checkbox" onchange="activateAmountPaid(this)" value="{{ $invoicepayment->invoice_id }}" name="invoice_id[]">
                                    <span class="form-check-sign"></span>
                                  </label>
                                </td>
                                <td class="p-0">
                                  {{ date('d-m-Y', strtotime($invoicepayment->invoice->created_at)) }}
                                </td>
                                <td class="p-0">
                                  <a href="/invoices/{{ $invoicepayment->invoice->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                                    VIEW INVOICE - {{ $invoicepayment->invoice->invoice_prefix }}{{ $invoicepayment->invoice->invoice_suffix }}
                                  </a>
                                </td>
                                <td class="p-0">
                                  {{ $invoicepayment->invoice->amount_due }}
                                </td>
                                <td class="p-0">
                                  <div class="form-group">
                                    <input type="number" step=".01" name="amount_paid[]" value="{{ $invoicepayment->amount_applied }}" onkeyup="updateTotal(this)" class="form-control p-1 border border-dark rounded-sm amount-paid">
                                  </div>
                                </td>
                                <td>
                                    <input type="checkbox" value="{{ $invoicepayment->id }}" class="remove-payment" onchange="removePayment(this)" name="remove_invoice_payment_id[]">
                                </td>
                              </tr>
                              @empty
                              <tr>
                                <td colspan="100%">
                                  No invoices associated with payment
                                </td>
                              </tr>
                              @endforelse
                              <tr>
                                <td colspan="100%" class="font-weight-bold h4">Other Invoices</td>
                              </tr>
                              <?php
                              $matches = 0;
                              ?>
                              @foreach($customer->invoices as $invoice)
                              @if($invoice->amount_due <= 0)
                                @continue
                              @endif
                              <?php
                              $matches += 1;
                              ?>
                              <tr attr-customer-id="{{ $invoice->customer_id }}" class="invoice-item">
                                <td class="text-center p-0">
                                  <label class="form-check-label">
                                    <input class="" type="checkbox" onchange="activateAmountPaid(this, true)" value="{{ $invoice->id }}" name="new_invoice_id[]">
                                    <span class="form-check-sign"></span>
                                  </label>
                                </td>
                                <td class="p-0">
                                  {{ date('d-m-Y', strtotime($invoice->created_at)) }}
                                </td>
                                <td class="p-0">
                                  <a href="/invoices/{{ $invoice->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                                    VIEW INVOICE - {{ $invoice->invoice_prefix }}{{ $invoice->invoice_suffix }}
                                  </a>
                                </td>
                                <td class="p-0">
                                  {{ $invoice->amount_due }}
                                </td>
                                <td class="p-0">
                                  <div class="form-group">
                                    <input type="number" step=".01" name="new_amount_paid[]" disabled max="{{ $invoice->amount_due }}" onkeyup="updateTotal(this)" class="form-control p-1 border border-dark rounded-sm amount-paid">
                                  </div>
                                </td>
                                <td>
                                  N/A
                                </td>
                              </tr>
                              @endforeach
                              @if($matches < 1) 
                              <tr>
                                <td colspan="100%">No invoices with overdues for customer</td>
                                </tr>
                              @endif

                            </tbody>
                          </table>
                        </div>
                      </div>

                    </div>
                    <div class="text-center w-100 mt-2">
                      <button type="submit" name="submit_btn" value="Create" class="btn btn-success w-75 mx-auto">EDIT</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'select2' => true,
  ]
  )
  @include('universal-layout.alert')
  <script type="text/javascript">
    function activateAmountPaid(checkbox, edit = false) {
      const tableRow = checkbox.closest('tr');
      if (checkbox.checked) {
        if(edit){
          tableRow.querySelector('input[name="new_amount_paid[]"]').disabled = false;
        }else{
          tableRow.querySelector('input[name="amount_paid[]"]').disabled = false;
        }
      } else {
        if(edit){
          tableRow.querySelector('input[name="new_amount_paid[]"]').disabled = true;
        }else{
          tableRow.querySelector('input[name="amount_paid[]"]').disabled = true;
        }
      }
    }

    function calculateInvoiceAmounts(amountInput) {
      const invoiceItems = document.querySelectorAll('.invoice-item');
      let totalAmount = amountInput.valueAsNumber;
      [...invoiceItems].reduce(spreadPayment, totalAmount);

    }

    function spreadPayment(accumulator, current) {
      const amountPaidInput = current.querySelector('input[name="amount_paid[]"]');
      const invoiceCheckboxInput = current.querySelector('input[name="invoice_id[]"]');
      if (current.classList.contains('d-none') || (accumulator < 0) || isNaN(accumulator)) {
        invoiceCheckboxInput.checked = false;
        amountPaidInput.disabled = true;
        amountPaidInput.value = 0;
      } else {
        if (accumulator > amountPaidInput.max) {
          invoiceCheckboxInput.checked = true;
          amountPaidInput.value = amountPaidInput.max;
          amountPaidInput.disabled = false;
          invoiceCheckboxInput.checked = true;
          return accumulator - amountPaidInput.max;
        } else {
          invoiceCheckboxInput.checked = true;
          amountPaidInput.disabled = false;
          amountPaidInput.value = accumulator;
          return 0;
        }
      }
    }

    function activateAmount(checkBox) {
      const form = checkBox.form;
      const grandTotal = form.amount;
      let parent = checkBox.parentElement.parentElement.parentElement.parentElement;
      let paidInput = parent.querySelector('input[name="amount_paid[]"]');
      if (checkBox.checked) {
        paidInput.disabled = false;
        paidInput.value = 0;
        const event = new Event('keyup');
        paidInput.dispatchEvent(event);
      } else {
        paidInput.disabled = true;
        grandTotal.value = grandTotal.value - paidInput.value;
        paidInput.value = '';
      }
    }

    function updateTotal(inputElement) {
      const form = inputElement.form;
      const grandTotal = form.amount;
      const amountPaid = form.querySelectorAll('.invoice-item');
      let totalPaid = 0;
      [...amountPaid].forEach((row) => {
        const item = row.querySelector('.amount-paid');
        if (!item.disabled) {
          totalPaid = parseInt(totalPaid) + parseInt(item.value);
        }
      })
      grandTotal.value = totalPaid;
      if (isNaN(totalPaid)) {
        grandTotal.value = 0;
      }
    }

    function removePayment(inputElement){
      const form = inputElement.form;
      const amountPaid = form.querySelectorAll('.invoice-item');
      [...amountPaid].forEach((row) => {
        const item = row.querySelector('.amount-paid');
        const removePayment = row.querySelector('.remove-payment');
        if(removePayment){
          if (removePayment.checked) {
            item.value = 0;
          }
        }
      });
      updateTotal(inputElement);
    }
  </script>
  @include('universal-layout.footer')