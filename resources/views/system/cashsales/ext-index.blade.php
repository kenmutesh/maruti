@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Cash Sales',
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
              <a href="/coatingjobs/create" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-5">
                <i class="tim-icons icon-simple-add"></i> CREATE JOB
              </a>
              <a href="/cashsales/direct" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-4">
                <i class="tim-icons icon-simple-add"></i> CREATE CASH SALE
              </a>
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title p-0 m-0">Cash Sales</h4>
                </div>
                <div class="card-body p-0">
                  <div class="py-3 row m-0">
                    <div class="col-sm-12 position-relative">
                      <input type="number" min="1" onblur="removeSearchAgedCashSalesBox(event)" onfocus="showSearchAgedCashSalesBox()" placeholder="Input cash sale number" oninput="searchAgedCashSales(this)" class="form-control cashsale-number-direct">
                      <div class="shadow col-8 bg-white p-0 position-absolute z-index d-none aged-cashsales-searchlist">

                      </div>
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
                  <div class="table-responsive">
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="open-list">
                      <thead class="text-primary">
                        <tr>
                          <th class="py-0 px-1 border">
                            Date
                          </th>
                          <th class="py-0 px-1 border">
                            Number
                          </th>
                          <th class="py-0 px-1 border">
                            Customer
                          </th>
                          <th class="py-0 px-1 border">
                            Grand Total
                          </th>
                          <th>
                            Jobcards
                          </th>
                          <th class="py-0 px-1 border">
                            Action
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($cashSales as $cashSale)
                        <tr>
                          <td class="p-0">
                            {{ $cashSale->created_at }}
                          </td>

                          <td class="p-0">
                            {{ $cashSale->ext_cash_sale_prefix }}{{ $cashSale->ext_cash_sale_suffix }}
                          </td>
                          <td class="p-0">
                            <span class="text-truncate d-block" data-toggle="tooltip" title="{{ $cashSale->customer->customer_name }}" style="max-width:3rem;cursor:default;">
                              {{ $cashSale->customer->customer_name ?? '' }}
                            </span>
                          </td>

                          <td class="p-0">
                            {{ number_format($cashSale->grand_total, 2) }}
                          </td>

                          <td class="p-0">
                            <div class="row flex-column m-0 p-1">
                              @if($cashSale->cancelled_at)
                              <span class="badge">N/A</span>
                              @elseif($cashSale->is_direct)
                              <span class="badge">DIRECT</span>
                              @else
                              @foreach($cashSale->coatingjobs as $coatingJob)
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
                            <a href="/cashsales/{{ $cashSale->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                              VIEW DOC
                            </a>

                            @if($cashSale->cancelled_at)
                            <span class="text-danger">Cash Sale Cancelled</span>
                            @else
                            @can('update', App\Models\CashSale::class)
                            @if ($cashSale->is_direct === false)
                            <a href="/cashsales/undo/{{ $cashSale->id }}" class="btn btn-danger btn-sm">
                              UNDO
                            </a>
                            @endif

                            @can('accounting')
                            <a href="{{ route('cashsales.edit', $cashSale->id) }}" class="btn btn-primary btn-sm">EDIT</a>
                            @endcan

                            <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelCashSaleForm{{ $cashSale->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="CANCEL CASH SALE">
                                CANCEL
                              </span>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="cancelCashSaleForm{{ $cashSale->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Cancel Cash Sale</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('cashsales.destroy', $cashSale->id ) }}">
                                      @csrf
                                      @method("DELETE")

                                      <p class="text-center">
                                        Are you sure you want to cancel this cash sale?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Cash Sale" class="btn btn-danger">YES</button>
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

    const searchResultBox = document.querySelector('.aged-cashsales-searchlist');
    async function searchAgedCashSales(inputElement) {
      searchResultBox.classList.remove('d-none');
      searchResultBox.innerHTML = '<div class="text-center py-2"><div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span></div></div>';

      if (inputElement.valueAsNumber > 9) {

        debounce(async () => {

          const agedCardsRequest = await fetch(`/cashsales/aged/${inputElement.valueAsNumber}`);

          if (agedCardsRequest.ok) {
            const response = await agedCardsRequest.text();

            searchResultBox.innerHTML = response;
          } else {
            searchResultBox.innerHTML = '<span class="text-danger px-3 h6">Error in searching!</span>';
          }
        })();
      }
    }

    function removeSearchAgedCashSalesBox(event) {
      if (!searchResultBox.contains(event.relatedTarget)) {
        searchResultBox.classList.add('d-none');
      }
    }

    function showSearchAgedCashSalesBox() {
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