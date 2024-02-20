@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Quotations',
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
        <div class="col">

          <div class="card card-plain my-4">
            <div class="card-header">
              <h4 class="card-title m-0 p-0">Quotations</h4>
            </div>
            <div class="card-body p-0">
                <div class="py-3 row m-0">
                <div class="col-sm-12 position-relative">
                    <input type="number" min="1" onblur="removeSearchAgedCardsBox()" placeholder="Input quotation number" oninput="searchAgedCards(this)" class="form-control job-card-number-direct">
                    <div class="shadow col-8 bg-white p-0 position-absolute z-index d-none aged-coatingjobs-searchlist">

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
                        Quotation Number
                      </th>
                      <th class="py-0 px-1 border">
                        Customer Name
                      </th>
                      <th class="py-0 px-1 border">
                        Belongs To
                      </th>
                      <th class="py-0 px-1 border">
                        Actions
                      </th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($quotations as $quotation)
                      <tr>
                        <td class="p-0">
                          {{ $quotation->created_at }}
                        </td>
                        <td class="p-0">
                          {{ $quotation->quotation_prefix }}{{ $quotation->quotation_suffix }}
                        </td>

                        <td class="p-0">
                          <span class="text-truncate d-block" data-toggle="tooltip" title="{{ $quotation->customer->customer_name ?? '' }}" style="max-width:5rem;cursor:default;">
                            {{ $quotation->customer->customer_name }}
                          </span>
                        </td>

                        <td class="p-0">
                          {{ $quotation->belongs_to->humanreadablestring() }}
                        </td>

                        <td class="d-flex p-0 w-100">

                            <a target="_blank" class="btn btn-sm btn-info" href="/coatingjobs/{{ $quotation->id }}">
                              QUOTATION DOCUMENT
                            </a>

                            <a href="{{ route('coatingjobs.edit', $quotation->id ) }}" type="button" name="button" class="btn btn-sm btn-info" data-toggle="tooltip" title="Edit Job Card">
                              <i class="material-icons">mode_edit</i>
                            </a>

                            <a class="btn btn-sm btn-info" href="/coatingjobs/quotations/convert/{{ $quotation->id }}">
                              CONVERT TO JOB CARD
                            </a>

                        </td>

                        <td class="p-0">

                          <button type="button" name="button" class="btn btn-sm btn-danger mb-2" data-toggle="modal" data-target="#deleteCoatingJobForm{{ $quotation->id }}">
                            <span class="d-block w-100 h-100" data-toggle="tooltip" title="Delete/Cancel Job Card">
                              <i class="material-icons">delete</i>
                            </span>
                          </button>

                          <!-- Modal -->
                          <div class="modal fade" id="deleteCoatingJobForm{{ $quotation->id }}" tabindex="-1" role="dialog">
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

                                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('coatingjobs.destroy', $quotation->id ) }}">
                                    @csrf
                                    @method("DELETE")
                                    <input type="hidden" name="job_id" value="{{ $quotation->id }}">

                                    <p class="text-center">
                                      Are you sure you want to cancel this coating job?
                                    </p>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                  <button type="submit" name="submit_btn" value="Delete Coating Job" class="btn btn-danger">YES</button>
                                  </form>
                                </div>
                              </div>
                                </div>
                              </div>
                            </div>

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

    const searchResultBox = document.querySelector('.aged-coatingjobs-searchlist');
    async function searchAgedCards(inputElement) {
      searchResultBox.classList.remove('d-none');
      searchResultBox.innerHTML = '<div class="text-center py-2"><div class="spinner-border text-dark" style="width:2rem; height:2rem;" role="status"><span class="sr-only"></span></div></div>';

      if (inputElement.valueAsNumber > 999) {

        debounce(async () => {

          const agedCardsRequest = await fetch(`/coatingjobs/quotations/aged/${inputElement.valueAsNumber}`);
          
          if(agedCardsRequest.ok){
            const response = await agedCardsRequest.text();
  
            searchResultBox.innerHTML = response;
          }else{
            searchResultBox.innerHTML = '<span class="text-danger px-3 h6">Error in searching!</span>';
          }
        })();
      }
    }

    function removeSearchAgedCardsBox() {
      searchResultBox.classList.add('d-none');
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