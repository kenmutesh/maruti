@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Coating Jobs List',
'select2bs4' => true,
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
  'slug' => '/coatingjobs'
  ]
  )

  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">

        <div class="content">
          <div class="col p-0">

            <div class="card card-plain my-2">
              <div class="card-header">
                <h4 class="card-title m-0 p-0">Open Jobs cards</h4>
              </div>
              <div class="card-body p-0">
                <div class="py-3 row m-0">
                  <div class="col-sm-8 position-relative">
                    <input type="number" min="1" onblur="removeSearchAgedCardsBox(event)" onfocus="showSearchAgedCardsBox()" placeholder="Input job card number" oninput="searchAgedCards(this)" class="form-control job-card-number-direct">
                    <div class="shadow col-8 bg-white p-0 position-absolute z-index d-none aged-coatingjobs-searchlist">

                    </div>
                  </div>
                  <div class="col-sm-4 text-center">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                        Other Coating Jobs
                      </button>
                      <div class="dropdown-menu">
                        @forelse ($allCoatingJobs as $singleCoatingJobBatch)
                        <li class="dropdown-item d-flex align-items-baseline">
                          <?php $coatingJobSection = ''; ?>
                          @foreach ($singleCoatingJobBatch as $singleCoatingJob)
                          @if ($loop->first)
                          <?php $coatingJobSection .= '/' . $singleCoatingJob->coating_suffix; ?>
                          {{ $singleCoatingJob->coating_prefix }}{{ $singleCoatingJob->coating_suffix }} -
                          @elseif($loop->last)
                          <?php $coatingJobSection .= '/' . $singleCoatingJob->coating_suffix; ?>
                          {{ $singleCoatingJob->coating_prefix }}{{ $singleCoatingJob->coating_suffix }}
                          @endif
                          @endforeach
                          <a href="/coatingjobs/sections{{ $coatingJobSection }}" class="btn btn-primary btn-sm py-0 px-2">View</a>
                        </li>
                        @empty
                        <li><a class="dropdown-item" href="#">No coating jobs present</a></li>
                        @endforelse
                      </div>
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
                <div class="table-responsive p-2 mt-sm-n2">
                  <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="open-list">
                    <thead class="text-primary">
                      <tr>
                        <th class="py-0 px-1 border">
                          Date Created
                        </th>
                        <th class="py-0 px-1 border">
                          Job Number
                        </th>
                        <th class="py-0 px-1 border" style="width: 4rem;">
                          Customer Name
                        </th>
                        <th class="py-0 px-1 border">
                          Belongs To
                        </th>
                        <th class="py-0 px-1 border">
                          Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      @foreach($coatingJobs as $coatingJob)
                      <tr>
                        <td class="p-0">
                          {{ $coatingJob->created_at }}
                        </td>
                        <td class="p-0">
                          {{ $coatingJob->coating_prefix }}{{ $coatingJob->coating_suffix }}
                        </td>

                        <td class="p-0">
                          <span class="text-truncate d-block" data-toggle="tooltip" title="{{ $coatingJob->customer->customer_name ?? '' }}" style="max-width:5rem;cursor:default;">
                            {{ $coatingJob->customer->customer_name ?? '' }}
                          </span>
                        </td>

                        <td class="p-0">
                          {{ $coatingJob->belongs_to->humanreadablestring() }}
                        </td>

                        <td class="d-flex w-100 p-0">
                          @can('viewAny', App\Models\CoatingJob::class)
                          <a target="_blank" class="btn btn-sm btn-info" href="/coatingjobs/{{ $coatingJob->id }}?hideprice=true">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="Print Out Job Card">
                              JOB CARD DOC
                            </span>
                          </a>

                          <a target="_blank" class="btn btn-sm btn-info" href="/coatingjobs/{{ $coatingJob->id }}">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="Print Out Job Card">
                              QUOTATION DOC
                            </span>
                          </a>
                          @endcan

                          <button class="btn btn-sm btn-info" type="button" name="button" data-toggle="modal" data-target="#createInvoice{{ $coatingJob->id }}" attr-data-coatingjob-id="{{ $coatingJob->id }}" attr-data-customer-id="{{ $coatingJob->customer_id }}" onclick="populateCoatingJobs(this)">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="Make an invoice">
                              INVOICE
                            </span>
                          </button>
                          <!-- Invoice modal start -->
                          <div class="modal fade" style="z-index: 5;" id="createInvoice{{ $coatingJob->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog mt-n3" role="document" style="transform: translateY(10px);">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">
                                    Create An Invoice</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <i class="tim-icons icon-simple-remove"></i>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <form onsubmit="showSpinner(event)" action="{{ route('invoices.store') }}" method="post" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="job_id" value="{{ $coatingJob->id }}">
                                    <input type="hidden" name="customer_id" value="{{ $coatingJob->customer->id }}">

                                    <div class="col-sm-12 d-none">
                                      <div class="form-check m-0 mb-2">
                                        <label class="form-check-label">
                                          <input class="form-check-input" type="checkbox" name="external" id="inlineCheckbox1" value="1">Treat as external
                                          <span class="form-check-sign"></span>
                                        </label>
                                        <p>
                                          Next External: {{ $ext_invoice }}
                                        </p>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">Discount</label>
                                          <input type="text" value="0" class="form-control" name="discount">
                                        </div>
                                      </div>

                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">CU Number</label>
                                          <div class="row">
                                            <input class="col-6 form-control" type="text" name="cu_number_prefix" value="{{ $cu_prefix }}">
                                            <input class="col-6 form-control" type="number" step=".1" name="cu_number_suffix" value="{{ $cu_suffix }}">
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="col-sm-12">
                                      <label class="m-0 text-wrap">
                                        Combine Job Cards from
                                        <b>{{ $coatingJob->customer->customer_name ?? '' }}</b>
                                        (Type job card number below)
                                      </label>
                                      <div class="row">
                                        <div class="col-sm-12 position-relative">
                                          <div class="position-absolute bg-white h-100 w-100 select-dropwdown-loader-{{ $coatingJob->id }} d-none">
                                            <div class="text-center py-2">
                                              <div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span>
                                              </div>
                                            </div>
                                          </div>
                                          <select class="form-control ms select-dropdown-{{ $coatingJob->id }}" multiple name="combined_jobcards[]">

                                          </select>
                                        </div>

                                      </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" value="Create Invoice" name="submit_btn" class="btn btn-primary">Create
                                    Invoice
                                    {{ $invoice }}</button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Invoice modal end -->
                          <button class="btn btn-sm btn-info" type="button" name="button" data-toggle="modal" data-target="#createCashSale{{ $coatingJob->id }}" attr-data-coatingjob-id="{{ $coatingJob->id }}" attr-data-customer-id="{{ $coatingJob->customer_id }}" onclick="populateCoatingJobsCashSale(this)">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="Make a cash sale">
                              CASH SALE
                            </span>
                          </button>
                          <!-- Cash sale modal start -->
                          <div class="modal fade" style="z-index: 5;" id="createCashSale{{ $coatingJob->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="transform: translateY(10px);">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">
                                    Create Cash Sale</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <i class="tim-icons icon-simple-remove"></i>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <form onsubmit="showSpinner(event)" action="{{ route('cashsales.store') }}" method="post" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="job_id" value="{{ $coatingJob->id }}">
                                    <input type="hidden" name="customer_id" value="{{ $coatingJob->customer->id }}">

                                    <div class="col-sm-12">
                                      <div class="form-check m-0 mb-2">
                                        <label class="form-check-label">
                                          <input class="form-check-input" type="checkbox" name="external" id="inlineCheckbox1" value="1">Treat as external
                                          <span class="form-check-sign"></span>
                                        </label>
                                        <p>
                                          Next External: {{ $ext_cashsale }}
                                        </p>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">Discount</label>
                                          <input type="text" value="0" class="form-control" name="discount">
                                        </div>
                                      </div>

                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">CU Number</label>
                                          <div class="row">
                                            <input class="col-6 form-control" type="text" name="cu_number_prefix" value="{{ $cu_prefix }}">
                                            <input class="col-6 form-control" type="number" step=".1" name="cu_number_suffix" value="{{ $cu_suffix }}">
                                          </div>
                                        </div>
                                      </div>
                                    </div>


                                    <div class="col-sm-12">

                                      <label class="m-0">Combine Job Cards from
                                        <b>{{ $coatingJob->customer->customer_name ?? '' }}</b>
                                        <div class="position-relative">
                                          <div class="position-absolute bg-white h-100 w-100 select-dropwdown-cashsale-loader-{{ $coatingJob->id }} d-none">
                                            <div class="text-center py-2">
                                              <div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span>
                                              </div>
                                            </div>
                                          </div>
                                          <select class="form-control ms select-dropdown-cashsale-{{ $coatingJob->id }}" multiple name="combined_jobcards[]">
                                          </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" value="Create Cash Sale" name="submit_btn" class="btn btn-primary">Create
                                    Cash Sale
                                    {{ $cashsale }}
                                  </button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Cash sale modal end -->
                          @can('update', [App\Models\CoatingJob::class, $coatingJob])
                          <a href="{{ route('coatingjobs.edit', $coatingJob->id ) }}" type="button" name="button" class="btn btn-sm btn-info" data-toggle="tooltip" title="Edit Job Card">
                            <i class="material-icons">mode_edit</i>
                          </a>
                          <button type="button" name="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteCoatingJobForm{{ $coatingJob->id }}">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="Cancel Job Card">
                              <i class="material-icons">cancel</i>
                            </span>
                          </button>
                          <div class="modal fade" style="z-index: 5;" id="deleteCoatingJobForm{{ $coatingJob->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">
                                    Cancel Coating Job
                                  </h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <i class="tim-icons icon-simple-remove"></i>
                                  </button>
                                </div>
                                <div class="modal-body">

                                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('coatingjobs.destroy', $coatingJob->id ) }}">
                                    @csrf
                                    @method("DELETE")
                                    <input type="hidden" name="job_id" value="{{ $coatingJob->id }}">

                                    <p class="text-center">
                                      Are you sure you want to cancel this coating
                                      job?
                                    </p>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                      <button type="submit" name="submit_btn" value="Delete Coating Job" class="btn btn-danger">YES</button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          @endcan

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
  </section>

  <script type="text/javascript">
    function toggleManualInvoice(selectElement) {
      if (selectElement.value == '-') {
        selectElement.nextElementSibling.disabled = false;
      } else {
        selectElement.nextElementSibling.disabled = true;
      }

    }
  </script>

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

    const searchResultBox = document.querySelector('.aged-coatingjobs-searchlist');

    async function searchAgedCards(inputElement) {
      searchResultBox.classList.remove('d-none');
      searchResultBox.innerHTML =
        '<div class="text-center py-2"><div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span></div></div>';

      if (inputElement.valueAsNumber > 999) {

        debounce(async () => {

          const agedCardsRequest = await fetch(`/coatingjobs/aged/${inputElement.valueAsNumber}`);

          if (agedCardsRequest.ok) {
            const response = await agedCardsRequest.text();

            searchResultBox.innerHTML = response;
          } else {
            searchResultBox.innerHTML =
              '<span class="text-danger px-3 h6">Error in searching!</span>';
          }
        })();
      }
    }

    function removeSearchAgedCardsBox() {
      if (!searchResultBox.contains(event.relatedTarget)) {
        searchResultBox.classList.add('d-none');
      }
    }

    function showSearchAgedCardsBox() {
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

    async function populateCoatingJobs(invoiceButton) {

      const coatingJobID = invoiceButton.getAttribute('attr-data-coatingjob-id');

      const customerID = invoiceButton.getAttribute('attr-data-customer-id');

      const customerSelectDropdown = document.querySelector(`.select-dropdown-${ coatingJobID }`);

      const customerSelectDropdownLoader = document.querySelector(`.select-dropwdown-loader-${ coatingJobID }`);

      customerSelectDropdownLoader.classList.remove('d-none');

      if ($(customerSelectDropdown).data("select2")) {
        $(customerSelectDropdown).select2('destroy');
      }
      const openCoatingJobsRequest = await fetch(`/coatingjobs/open/${customerID}/${coatingJobID}`);

      if (openCoatingJobsRequest.ok) {
        const response = await openCoatingJobsRequest.text();

        customerSelectDropdown.innerHTML = response;
      } else {
        customerSelectDropdown.innerHTML = '<option disabled>Error in getting jobcards</option>';
      }
      $(customerSelectDropdown).select2();
      customerSelectDropdownLoader.classList.add('d-none');
    }

    async function populateCoatingJobsCashSale(cashsaleButton) {
      const coatingJobID = cashsaleButton.getAttribute('attr-data-coatingjob-id');

      const customerID = cashsaleButton.getAttribute('attr-data-customer-id');

      const customerSelectDropdown = document.querySelector(`.select-dropdown-cashsale-${ coatingJobID }`);

      const customerSelectDropdownLoader = document.querySelector(
        `.select-dropwdown-cashsale-loader-${ coatingJobID }`);

      customerSelectDropdownLoader.classList.remove('d-none');

      if ($(customerSelectDropdown).data("select2")) {
        $(customerSelectDropdown).select2('destroy');
      }
      const openCoatingJobsRequest = await fetch(`/coatingjobs/open/${customerID}/${coatingJobID}`);

      if (openCoatingJobsRequest.ok) {
        const response = await openCoatingJobsRequest.text();

        customerSelectDropdown.innerHTML = response;
      } else {
        customerSelectDropdown.innerHTML = '<option disabled>Error in getting jobcards</option>';
      }
      $(customerSelectDropdown).select2();
      customerSelectDropdownLoader.classList.add('d-none');
    }
  </script>
  @include('universal-layout.footer')