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
                  <h4 class="card-title p-0 m-0">Create Payment</h4>
                </div>
                <div class="card-body p-0">
                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('supplier-payments.store') }}">
                    @csrf
                    <div class="d-flex flex-column">

                      <div class="row">
                        <div class="col-sm-5 position-relative mr-5">
                          <div class="form-group">
                            <label>Supplier</label>
                            <select onchange="searchSupplierPurchases(this)" class="form-control ms searchable-select customer-select" data-live-search="true" data-style="rounded-sm text-white m-0" required name="supplier_id">
                              <option disabled selected>Choose a supplier</option>
                              <option value="" data-tokens="ALL">ALL</option>
                              @foreach($suppliers as $supplier)
                              <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->supplier_name }}">
                                {{ $supplier->supplier_name }}
                              </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group row align-items-center">
                            <label>Payment Mode</label>
                            <select name="payment_mode" id="" required class="form-control searchable-select ms">
                              <option disabled selected>Choose payment type</option>
                              @foreach($paymentTypes as $payment)
                              <option value="{{ $payment->value }}">
                                {{ $payment->humanreadablestring() }}
                              </option>
                              @endforeach
                            </select>

                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step=".01" class="form-control p-1 border-dark rounded-sm" oninput="calculateInvoiceAmounts(this)" required name="amount">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>Code/References</label>
                            <input required type="text" class="form-control p-1 border-dark rounded-sm" name="transaction_ref">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>Date</label>
                            <input type="date" required class="form-control p-1 border-dark rounded-sm" name="payment_date">
                          </div>
                        </div>

                        <div class="table-responsive mt-2">
                          <table class="table table-bordered text-center">
                            <thead>
                              <th class="p-0 border-top"></th>
                              <th class="p-0 border-top">Purchase Date</th>
                              <th class="p-0 border-top">Purchase No</th>
                              <th class="p-0 border-top">Amount Due</th>
                              <th class="p-0 border-top">Paid Amount</th>
                            </thead>
                            <tbody class="invoice-body">
                              <tr class="placeholder">
                                <td colspan="100%">Choose supplier first</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                    </div>
                    <div class="text-center w-100 mt-2">
                      <button type="submit" name="submit_btn" value="Create" class="btn btn-success w-75 mx-auto">CREATE</button>
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
    function activateAmountPaid(checkbox) {
      const tableRow = checkbox.closest('tr');
      if (checkbox.checked) {
        tableRow.querySelector('input[name="amount_paid[]"]').disabled = false;
      } else {
        tableRow.querySelector('input[name="amount_paid[]"]').disabled = true;
      }
    }

    function calculateInvoiceAmounts(amountInput) {
      const invoiceItems = document.querySelectorAll('.invoice-item');
      let totalAmount = amountInput.valueAsNumber;
      [...invoiceItems].reduce(spreadPayment, totalAmount);

    }

    function spreadPayment(accumulator, current) {
      const amountPaidInput = current.querySelector('input[name="amount_paid[]"]');
      const invoiceCheckboxInput = current.querySelector('input[name="purchase_order_id[]"]');
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
        } else if(accumulator > 0) {
          invoiceCheckboxInput.checked = true;
          amountPaidInput.disabled = false;
          amountPaidInput.value = parseFloat(accumulator).toFixed(2);
          return 0;
        }else{
          invoiceCheckboxInput.checked = false;
        amountPaidInput.disabled = true;
        amountPaidInput.value = 0;
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
      const amountPaid = form.querySelectorAll('input[name="amount_paid[]"]');
      let totalPaid = 0;
      [...amountPaid].forEach((item) => {
        if (!item.disabled) {
          totalPaid = parseInt(totalPaid) + parseInt(item.value);
        }
      })
      grandTotal.value = totalPaid;
      if (isNaN(totalPaid)) {
        grandTotal.value = 0;
      }
    }

    const searchResultBox = document.querySelector('.invoice-body');
    async function searchSupplierPurchases(selectElement) {
      searchResultBox.innerHTML = '<tr class="text-danger px-3 h6"><td colspan="100%"><div class="text-center py-2"><div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span></div></div></td></tr>';
      debounce(async () => {

        const customerInvoicesRequest = await fetch(`/suppliers/purchases/${selectElement.value}`);

        if (customerInvoicesRequest.ok) {
          const response = await customerInvoicesRequest.text();

          searchResultBox.innerHTML = response;
        } else {
          searchResultBox.innerHTML = '<tr class="text-danger px-3 h6"><td colspan="100%">Error in searching!<td></tr>';
        }
      })();
    }

    function debounce(func, timeout = 1500) {
      let timer;
      return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          func.apply(this, args);
        }, timeout);
      };
    }
  </script>
  @include('universal-layout.footer')