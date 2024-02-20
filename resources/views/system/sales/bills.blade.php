@include('system.layout.header', ['pageTitle' => 'Aprotec | Sales'])

<body class="">
  @include('system.layout.spinner')
  <div class="wrapper">
    @include('system.layout.sidemenu', ['currentPage' => 'payment'])
    <div class="main-panel">
      @include('system.layout.topnav', ['breadcrumb' => 'SALES'])

      <div class="content">

        <div class="row">
          <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 w-25" data-toggle="modal" data-target="#createLocationForm">
            <i class="tim-icons icon-simple-add"></i> CREATE A BILL
          </button>

          <!-- Modal -->
          <div class="modal fade" id="createLocationForm" tabindex="-1" role="dialog" aria-labelledby="createCompanyForm" aria-hidden="true">
            <div class="modal-dialog modal-dialog modal-xl" role="document" style="margin-top:-40px;">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Create A Payment in the System</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="tim-icons icon-simple-remove"></i>
                  </button>
                </div>
                <div class="modal-body">

                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('add_payment') }}">
                    @csrf
                    <div class="d-flex flex-column">

                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Customer</label>
                            <select class="form-control searchable-select customer-select" required name="customer_id" onchange="filterInvoices(this)">
                              <option disabled selected>Choose a customer</option>
                              @foreach($customers as $singleCustomer)
                                <option value="{{ $singleCustomer->id }}">
                                  {{ $singleCustomer->customer_name }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Transaction Mode</label>
                            <input type="text" required name="transaction_mode" class="form-control" list="transaction_modes" />
                            <datalist id="transaction_modes">
                              <option value="Cash">Cash</option>
                              <option value="Cheque">Cheque</option>
                              <option value="M-Pesa">M-Pesa</option>
                              <option value="WTH VAT">WTH VAT</option>
                              <option value="WTH TAX">WTH TAX</option>
                              <option value="RTGS">RTGS</option>
                              <option value="Pesalink">Pesalink</option>
                            </datalist>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Amount Paid</label>
                            <input type="text" class="form-control" readonly required name="amount">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Transaction Code/References</label>
                            <input type="text" class="form-control" name="transaction_ref">
                          </div>
                        </div>

                        <ul class="list-group col-sm-11 mx-auto">
                          Select Invoices
                            @foreach($invoices as $singleInvoice)
                             @if($singleInvoice->payment_status != 'PAID')
                               <li class="list-group-item d-flex justify-content-around align-items-center invoice-item" attr-customer-id="{{ $singleInvoice->customer_id }}">

                                 <span>
                                   <div class="form-check form-check-inline" style="transform: translateY(-13px);">
                                     <label class="form-check-label">
                                       <input class="form-check-input" type="checkbox"
                                       value="{{ $singleInvoice->id }}-{{ $singleInvoice->job_id }}&{{ $singleInvoice->invoice_prefix }}-{{ $singleInvoice->invoice_suffix }}-{{ $singleInvoice->balance }}"
                                       name="invoice[]" onchange="activateAmount(this)">
                                       <span class="form-check-sign"></span>
                                     </label>
                                   </div>
                                 </span>

                                 <span>
                                   {{ $singleInvoice->invoice_prefix }}-{{ $singleInvoice->invoice_suffix }}
                                   <a href="/sales/invoice/viewdoc/{{ $singleInvoice->id }}/{{ $singleInvoice->job_id }}" target="_blank" type="button" name="button" class="btn btn-info mb-2" >
                                     <i class="tim-icons icon-paper"></i>
                                   </a>
                                 </span>

                                 <span>
                                   {{ $singleInvoice->payment_status }}
                                 </span>

                                 <span>
                                   DUE: {{ $singleInvoice->balance }}
                                 </span>
                                 <span>

                                   <div class="form-group">
                                     <label for="">Amount Paid</label>
                                      <input type="text" name="amount_paid[]" onkeyup="updateTotal(this)" class="form-control" disabled>
                                   </div>

                                 </span>

                               </li>
                             @endif
                          @endforeach
                        </ul>
                      </div>

                    </div>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                  <button type="submit" name="submit_btn" value="Create Location" class="btn btn-primary">CREATE A PAYMENT</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title">Available Payments</h4>
              <p class="category">List of payments present in the system in order of recent registrations</p>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table tablesorter data-table" id="">
                  <thead class="text-primary">
                    <tr>
                      <th>
                        Date Created
                      </th>
                      <th>
                        Payment Mode
                      </th>
                      <th>
                        Transaction Reference
                      </th>
                      <th>
                        Amount Paid
                      </th>
                      <th>
                        Customer Name
                      </th>
                      <th>
                        Invoice Referenced
                      </th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($payments as $singlePayment)
                        <tr>
                          <td>
                            {{ $singlePayment->date_created }}
                          </td>

                          <td>
                            {{ $singlePayment->payment_mode }}
                          </td>

                          <td class="text-center">
                            {{ $singlePayment->transaction_ref }}
                          </td>

                          <td>
                            {{ $singlePayment->amount}}
                          </td>

                          <td>
                            {{ $singlePayment->customer->customer_name }}
                          </td>
                          <td>
                              <?php
                                  $invoiceParts = explode('&', $singlePayment->referenced_invoice);
                                  $jobDetails = explode('-', $invoiceParts[0]);
                                  $invoiceDetails = explode('-', $invoiceParts[1]);

                                  ?>
                                  <a href="/sales/invoice/viewdoc/{{ $jobDetails[0] }}/{{ $jobDetails[1] }}" target="_blank" type="button" name="button" class="btn btn-info mb-2"target="_blank">
                                    <i class="tim-icons icon-paper"></i>
                                    <?php echo $invoiceDetails[0].'-'.$invoiceDetails[1] ?>
                                  </a>
                          </td>
                        </tr>
                      @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      </div>
      <script type="text/javascript">
        function filterInvoices(selectElement) {
          const customerID = selectElement.value;

          const invoiceList = document.querySelectorAll('.invoice-item');

          [...invoiceList].forEach((invoice) => {
            if (invoice.getAttribute('attr-customer-id') != customerID) {
              invoice.style.setProperty('display', 'none', 'important');
            }else {
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
          }else {
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

@include('system.layout.footer', ['dataTable' => true, 'notifications' => true,])
