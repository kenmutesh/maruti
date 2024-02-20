@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Invoices',
'datatable' => true,
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

            <div class="row">
              <a href="/coatingjobs/create" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-4">
                <i class="tim-icons icon-simple-add"></i> CREATE JOB
              </a>
              <a href="/invoices/direct" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-4">
                <i class="tim-icons icon-simple-add"></i> CREATE DIRECT INVOICE
              </a>
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title m-0 p-0">External Invoices Upto {{ date('d/m/Y', time()) }}</h4>
                </div>
                <div class="card-body p-1">
                  <div class="row justify-content-between">
                    <div class="col-sm-8 position-relative">
                      <input type="number" min="1" onblur="removeSearchAgedInvoicesBox(event)" onfocus="showSearchAgedInvoicesBox()" placeholder="Input invoice number" oninput="searchAgedInvoices(this)" class="form-control invoice-number-direct">
                      <div class="shadow col-8 bg-white p-0 position-absolute z-index d-none aged-invoices-searchlist">

                      </div>
                    </div>
                  </div>
                  <div class="py-3 row m-0">
                    <div class="d-flex justify-content-around col p-0 open-list filters">
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
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="open-list">
                      <thead class="text-primary">
                        <tr>
                          <th class="py-0 px-1 border">
                            Date
                          </th>
                          <th class="py-0 px-1 border">
                            Invoice No.
                          </th>
                          <th class="py-0 px-1 border">
                            Customer
                          </th>
                          <th class="py-0 px-1 border">
                            Grand Total
                          </th>
                          @can('accounting')
                          <th class="py-0 px-1 border">
                            Open Balance
                          </th>
                          @endcan
                          <th class="py-0 px-1 border">
                            Jobcards
                          </th>
                          <th class="py-0 px-1 border">
                            Action
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                          <td class="p-0">
                            {{ date('d/m/Y', strtotime($invoice->created_at)) }}
                          </td>

                          <td class="p-0">
                            {{ $invoice->ext_invoice_prefix }}{{ $invoice->ext_invoice_suffix }}
                          </td>

                          <td class="p-0">
                            <span class="text-truncate d-block" data-toggle="tooltip" title="{{ $invoice->customer->customer_name ?? '' }}" style="max-width:3rem;cursor:default;">
                              {{ $invoice->customer->customer_name ?? '' }}
                            </span>
                          </td>

                          <td class="p-0">
                            {{ number_format($invoice->grand_total, 2) }}
                          </td>
                          @can('accounting')
                          <td class="p-0">
                            {{ number_format($invoice->amount_due, 2) }}
                          </td>
                          @endcan
                          <td class="p-0">
                            <div class="row flex-column m-0 p-1">
                              @if($invoice->cancelled_at)
                              <span class="badge">N/A</span>
                              @elseif($invoice->is_direct)
                              <span class="badge">DIRECT</span>
                              @else
                              @foreach($invoice->coatingjobs as $coatingJob)
                              @if($coatingJob->coating_suffix)
                              <a target="_blank" href="/coatingjobs/{{ $coatingJob->id }}?hideprice=true" class="btn btn-sm btn-info m-0 my-1 p-1">
                                {{ $coatingJob->coating_prefix }}{{ $coatingJob->coating_suffix }}
                              </a>
                              @endif
                              @endforeach
                              @endif
                            </div>
                          </td>

                          <td class="p-0">
                            <a href="/invoices/{{ $invoice->id }}" target="_blank" type="button" name="button" class="btn btn-sm btn-info">
                              VIEW DOC
                            </a>

                            @if($invoice->cancelled_at)
                            <span class="text-danger">Invoice Cancelled</span>
                            @else
                            @can('update', App\Models\Invoice::class)
                            @if ($invoice->is_direct === false)
                            <a href="/invoices/undo/{{ $invoice->id }}" class="btn btn-danger btn-sm">
                              UNDO
                            </a>
                            @endif
                            @can('accounting')
                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-primary btn-sm">EDIT</a>
                            @endcan
                            <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancleInvoiceForm{{ $invoice->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="CANCEL INVOICE">
                                CANCEL
                              </span>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="cancleInvoiceForm{{ $invoice->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Cancel Invoice</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('invoices.destroy', $invoice->id ) }}">
                                      @csrf
                                      @method("DELETE")

                                      <p class="text-center">
                                        Are you sure you want to cancel this invoice?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Bin" class="btn btn-danger">YES</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endcan
                            <!-- end modal -->
                            @endif
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
      </div>
    </div>
  </section>
  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'datatable' => true,

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

    const searchResultBox = document.querySelector('.aged-invoices-searchlist');
    async function searchAgedInvoices(inputElement) {
      searchResultBox.classList.remove('d-none');
      searchResultBox.innerHTML = '<div class="text-center py-2"><div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span></div></div>';

      if (inputElement.valueAsNumber > 99) {

        debounce(async () => {

          const agedCardsRequest = await fetch(`/invoices/aged/${inputElement.valueAsNumber}`);

          if (agedCardsRequest.ok) {
            const response = await agedCardsRequest.text();

            searchResultBox.innerHTML = response;
          } else {
            searchResultBox.innerHTML = '<span class="text-danger px-3 h6">Error in searching!</span>';
          }
        })();
      }
    }

    function removeSearchAgedInvoicesBox(event) {
      if (!searchResultBox.contains(event.relatedTarget)) {
        searchResultBox.classList.add('d-none');
      }
    }

    function showSearchAgedInvoicesBox() {
      searchResultBox.classList.remove('d-none');
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