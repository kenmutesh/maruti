@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Invoices',
'datatable' => true,
'select2bs4' => true,
]
)
<style>
  .table .form-check label .form-check-sign:before,
  .table .form-check label .form-check-sign:after {
    top: 0px;
  }

  .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
  }

  .modal-backdrop.show {
    z-index: 4;
  }

  .pagination .page-item.disabled>.page-link {
    color: #f00;
    padding: 0;
    cursor: not-allowed;
    opacity: 1;
  }
</style>

<body class="theme-green">

  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/sales'
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
                  <h4 class="card-title m-0 p-0">Invoices</h4>
                </div>
                <div class="card-body">
                  <div class="py-3 row m-0">
                    <div class="d-flex flex-column col-sm-6">
                      Pages: Only one page
                    </div>
                    <div class="d-flex justify-content-around col-sm-6 p-0 open-list filters">
                      <div class="d-flex flex-column col-5 col-sm-6 p-0">
                        <span>Min. Date</span>
                        <input type="date" name="min" class="min border-dark form-control rounded-0" id="">
                      </div>
                      <div class="d-flex flex-column col-5 col-sm-6 p-0">
                        <span>Max. Date</span>
                        <input type="date" name="max" class="max border-dark form-control rounded-0" id="">
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive p-2 mt-sm-n2">
                    <form action="{{ route('multi_edit_invoice') }}" method="POST">
                      @csrf
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="open-list">
                      <thead class="text-primary">
                        <tr>
                          <th class="py-0 px-1 border">
                            Date Created
                          </th>
                          <th class="py-0 px-1 border">
                            Invoice No.
                          </th>
                          <th class="py-0 px-1 border">
                            Customer Name
                          </th>
                          <th class="py-0 px-1 border">
                            Grand Total
                          </th>
                          <th class="py-0 px-1 border">
                            Action
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($invoices as $singleInvoice)
                        <tr>
                          <td class="p-0">
                            {{ date('Y-m-d', strtotime($singleInvoice->date_created)) }}
                          </td>

                          <td class="p-0">
                            {{ $singleInvoice->invoice_prefix }}{{ $singleInvoice->invoice_suffix }}
                          </td>

                          <td class="p-0">
                            <span class="text-truncate d-block" data-toggle="tooltip" title="{{ $singleInvoice->customer->company }}" style="max-width:5rem;cursor:default;">
                              {{ $singleInvoice->customer->customer_name }}
                            </span>
                          </td>

                          <td class="p-0">
                            {{ number_format($singleInvoice->net_amount, 2) }}
                          </td>

                          <td class="p-0">
                            <div class="d-flex flex-column">
                              <input type="hidden" name="invoice_id[]" value="{{ $singleInvoice->id }}">
                              <select class="ms search-select m-1" name="customer_invoice[]">
                                @foreach($customers as $customer)
                                  <option 
                                    <?php echo $selected = ($singleInvoice->customer->id == $customer->id) ?'selected' : '' ; ?>
                                    value="{{ $customer->id }}">
                                    {{ $customer->customer_name }}<?php echo $selected = ($singleInvoice->customer->id == $customer->id) ?'(current)' : '' ; ?>
                                  </option>
                                @endforeach
                              </select>

                              <?php
                              $coatingJobString = "";
                              foreach($singleInvoice->coatingjobs as $singleJob){
                                $coatingJobString .= $singleJob->coating_suffix.",";
                              }
                              echo count($singleInvoice->coatingjobs);
                              ?>

                              <input type="coating_jobs[]" class="w-75" value="{{ $coatingJobString }}">

                              <input type="date" name="invoice_date[]" value="{{ date('Y-m-d', strtotime($singleInvoice->date_created)) }}" class="m-1">
                              <button class="btn btn-primary m-1">
                                SAVE
                              </button>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    </form>
                  </div>
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
  'datatable' => true,
   
  'select2bs4' => true,
  ]
  )
  @include('universal-layout.alert')
  <script>
    const dataTableInstances = [];
    const filterDates = document.querySelectorAll('.filters');

    $.fn.dataTable.ext.search.push(
      function(settings, searchData, index, rowData, counter) {
        let filtersClass = settings.nTable.getAttribute('data-table');

        if (!filtersClass) {
          return true;
        }

        let minimumInput = document.querySelector(`.${filtersClass} .min`);

        let maximumInput = document.querySelector(`.${filtersClass} .max`);

        var min = new Date(minimumInput.value);
        var max = new Date(maximumInput.value);
        var date = new Date(searchData[0]) || 0;

        if ((isNaN(min) && isNaN(max)) ||
          (isNaN(min) && date <= max) ||
          (min <= date && isNaN(max)) ||
          (min <= date && age <= max)) {
          return true;
        }
        return false;
      }
    );

    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip(); //for tooltip functionality

      $('.data-table').each((index, element) => {
        const table = $(element).DataTable({
          paging: false,
          aaSorting: [],
        });

        dataTableInstances.push(table);
      })

    });

    filterDates.forEach((element, index) => {

      element.querySelector('.min').addEventListener('change', (e) => {
        dataTableInstances[index].draw();
      })

      element.querySelector('.max').addEventListener('change', (e) => {
        dataTableInstances[index].draw();
      })


    })
  </script>
  @include('universal-layout.footer')