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
                <h4 class="card-title m-0 p-0">Cancelled Jobs cards</h4>
              </div>
              <div class="card-body p-0">
                <div class="py-3 row m-0">
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
                          Date Cancelled
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
                          {{ $coatingJob->cancelled_at }}
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