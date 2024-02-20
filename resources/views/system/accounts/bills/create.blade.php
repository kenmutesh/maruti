@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Accounts Suppliers',
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

        <div class="content">


          <div class="col">
            <div class="card card-plain">
              <div class="card-header">
                <h4 class="card-title p-0 m-0">Create Bill Payment</h4>
              </div>
              <div class="card-body">
                <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('bills.store') }}">
                  @csrf
                  <div class="d-flex flex-column">

                    <div class="row">
                      <div class="col-sm-5 position-relative">
                        <div class="form-group">
                          <label>Supplier:</label>
                          <select class="form-control p-0 ms searchable-select customer-select" data-live-search="true" required name="supplier_id" onchange="filterInvoices(this)" data-style="m-0 text-white">
                            <option disabled selected>Choose a supplier</option>
                            <option value="" data-tokens="ALL">ALL</option>
                            @foreach($suppliers as $singleSupplier)
                            <option value="{{ $singleSupplier->id }}" data-tokens="{{ $singleSupplier->supplier_name }}">
                              {{ $singleSupplier->supplier_name }}
                            </option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group row align-items-center">
                        <div class="w-100">
                            <div class="dropdown">
                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle px-1" data-toggle="dropdown">
                                Add Payment Type
                              </button>
                              <div class="dropdown-menu p-1 text-center">
                                <div class="form-group">
                                  <label for="">Name</label>
                                  <input type="text" name="payment_name" class="form-control">
                                </div>
                                <button type="button" class="btn btn-success px-1 py-2" onclick="createPaymentType(this)">
                                  CREATE TYPE
                                </button>
                              </div>
                            </div>
                          </div>
                          <select name="transaction_mode" id="" class="form-control searchable-select type-dropdown ms">
                            <option disabled selected>Choose a transaction mode</option>
                            @foreach($paymentTypes as $payment)
                            <option value="{{ $payment->name }}">
                              {{ $payment->name }}
                            </option>
                            @endforeach
                          </select>
                          
                        </div>
                      </div>

                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>Amount:</label>
                          <input type="text" class="form-control p-1 border-dark rounded-sm" oninput="calculateInvoiceAmounts(this)" required name="amount">
                        </div>
                      </div>

                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>Code/References:</label>
                          <input type="text" class="form-control p-1 border-dark rounded-sm" name="transaction_ref">
                        </div>
                      </div>

                      <div class="col-sm-4">
                          <div class="form-group">
                            <label>Date</label>
                            <input type="date" required class="form-control p-1 border-dark rounded-sm" name="payment_date">
                          </div>
                        </div>

                      <ul class="list-group col-sm-12 mx-auto">
                        Select bills orders
                        @foreach($purchaseorders as $singlePurchase)
                        @if($singlePurchase->amount_due > 0)
                        <li class="list-group-item d-flex justify-content-around align-items-center invoice-item p-0" attr-customer-id="{{ $singlePurchase->supplier_id }}">

                          <span>
                            <div class="form-check form-check-inline" style="transform: translateY(-13px);">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" value="{{ $singlePurchase->id }}" name="po_id[]">
                                <span class="form-check-sign"></span>
                              </label>
                            </div>
                          </span>

                          <span>
                            {{ date('d-m-Y', strtotime($singlePurchase->date_created)) }}
                          </span>

                          <span>
                            {{ $singlePurchase->lpo_prefix }}{{ $singlePurchase->lpo_suffix }}
                            <a href="/purchaseorders/viewdoc/{{ $singlePurchase->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                              VIEW PO
                            </a>
                          </span>

                          <span class="due-text" data-original="{{ $singlePurchase->amount_due }}">
                            DUE: {{ $singlePurchase->amount_due }}
                          </span>
                          <span>

                            <div class="form-group">
                              <input type="text" name="amount_paid[]" data-max="{{ $singlePurchase->amount_due }}" onkeyup="updateTotal(this)" class="form-control p-1 border-dark rounded-sm">
                            </div>

                          </span>

                        </li>
                        @endif
                        @endforeach
                      </ul>
                    </div>

                  </div>
                  <div class="text-center w-100">
                    <button type="submit" name="submit_btn" value="Create Location" class="btn btn-primary w-75">CREATE</button>
                  </div>
                </form>
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
    function createPaymentType(buttonElement) {
      const paymentName = buttonElement.parentElement.querySelector('input').value;
      const data = {
        name: paymentName,
        type: 'Supplier Bill'
      }
      fetch('/payment-types', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify(data)
        })
        .then((response) => response.json())
        .then((json) => {
          const dropdown = document.querySelector('.type-dropdown');
          dropdown.innerHTML += `<option value="${json.name}">${json.name}</option>`;
          swal({
            title: 'Success',
            text: 'Added successfully',
            type: 'success',
            timer: 3000,
            buttons: false,
          });
        })
    }

    function calculateInvoiceAmounts(totalAmount) {
      const invoiceList = document.querySelectorAll('.invoice-item');

      let amount;

      if (totalAmount.value == '') {
        amount = 0;
      } else {
        amount = parseFloat(totalAmount.value);
      }

      const dueAmounts = [];
      const elementsSet = [];
      for (const invoice of invoiceList) {
        if (invoice.style.display == 'flex') {
          dueAmounts.push(invoice.querySelector('.due-text').getAttribute('data-original'));
          elementsSet.push(invoice);
        }
      }

      const remainders = [];

      dueAmounts.reduce((accumulator, currentValue) => {
        const working = accumulator - currentValue
        let trueRemaining = 0;
        if (working < 0) {
          trueRemaining = working * -1;
        }
        const data = {
          initial: accumulator,
          due: currentValue,
          remainder: working,
          trueValue: trueRemaining
        }
        remainders.push(data);

        if (working < 0) {
          return 0;
        } else {
          return working;
        }
      }, amount);

      for (const [index, parent] of elementsSet.entries()) {
        parent.querySelector('input[name="amount_paid[]"]').value = remainders[index].initial;
        if (remainders[index].initial > 0) {
          if (!parent.querySelector('input[type="checkbox"]').checked) {
            parent.querySelector('input[type="checkbox"]').click();
          }
        }else{
          parent.querySelector('input[type="checkbox"]').checked = false;
        }
      }


    }

    function filterInvoices(selectElement) {
      const customerID = selectElement.value;

      const invoiceList = document.querySelectorAll('.invoice-item');

      [...invoiceList].forEach((invoice) => {
        if (invoice.getAttribute('attr-customer-id') != customerID) {
          invoice.style.setProperty('display', 'none', 'important');
        } else {
          invoice.style.setProperty('display', 'flex', 'important');
        }
      })
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
  </script>

  @include('universal-layout.footer')