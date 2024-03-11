@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Invoices',
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
                  <h4 class="card-title m-0 p-0">Invoice No: {{ $invoice->invoice_prefix }}{{ $invoice->invoice_suffix }}</h4>
                </div>
                <div class="card-body p-1">
                  <form onsubmit="showSpinner(event)" method="POST" action="{{ route('invoices.update', $invoice->id) }}">
                  @csrf
                  @method('PUT')
                    <div class="row">
                    @if($invoice->external)
                    <input type="hidden" name="external" value="1">
                    <div class="form-group col-sm-6">
                      <label for="">Invoice Prefix</label>
                      <input type="text" name="ext_invoice_prefix" class="form-control" value="{{ $invoice->ext_invoice_prefix }}"/>
                    </div>
                    <div class="form-group col-sm-6">
                      <label for="">Invoice Suffix</label>
                      <input type="number" name="ext_invoice_suffix" class="form-control" value="{{ $invoice->ext_invoice_suffix }}"/>
                    </div>
                    @else
                    <div class="form-group col-sm-6">
                      <label for="">Invoice Prefix</label>
                      <input type="text" name="invoice_prefix" class="form-control" value="{{ $invoice->invoice_prefix }}"/>
                    </div>
                    <div class="form-group col-sm-6">
                      <label for="">Invoice Suffix</label>
                      <input type="number" name="invoice_suffix" class="form-control" value="{{ $invoice->invoice_suffix }}"/>
                    </div>
                    @endif

                      <div class="form-group col-sm-6">
                        <label for="">CU number prefix</label>
                        <input type="text" name="cu_number_prefix" class="form-control" value="{{ $invoice->cu_number_prefix }}">
                      </div>

                      <div class="form-group col-sm-6">
                        <label for="">CU number suffix</label>
                        <input type="number" name="cu_number_suffix" class="form-control" value="{{ $invoice->cu_number_suffix }}">
                      </div>

                      <div class="form-group col-sm-6">
                        <label for="">Open Balance</label>
                        <input type="number" name="open_balance" class="form-control" value="{{ $invoice->amount_due }}">
                      </div>
                    </div>

                    <button name="submit_btn" class="btn btn-success w-100">
                      EDIT INVOICE
                    </button>
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
  'select2bs4' => true   
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
          (min <= date && date <= max)) {
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