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

          <div class="card card-plain mb-4">
            <div class="card-header">
              <h4 class="card-title m-0 p-0">Closed Jobs cards</h4>
            </div>
            <div class="card-body p-0">
              <div class="py-3 row m-0">
                <div class="d-flex justify-content-around col-sm-6 p-0 closed-list filters">
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
                <table class="table table-bordered sorter data-table fixed-table col-12 table-fixed-2 data-table p-0" data-table="closed-list">
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
                        Invoice
                      </th>
                      <th class="py-0 px-1 border">
                        Cash Sale
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
                        {{ $coatingJob->customer->customer_name }}
                      </td>

                      <td class="p-0">
                        {{ $coatingJob->belongs_to->humanreadablestring() }}
                      </td>

                      <td class="p-0">
                        @if($coatingJob->invoice_id)
                        <a href="/invoices/{{ $coatingJob->invoice_id }}" target="_blank" type="button" name="button" class="btn btn-sm btn-info">
                          VIEW INVOICE
                        </a>
                        @else
                          -
                        @endif
                      </td>

                      <td class="p-0">
                        @if($coatingJob->cash_sale_id)
                          <a href="/cashsales/{{ $coatingJob->cash_sale_id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                            VIEW CASH SALE
                          </a>
                        @else
                          -
                        @endif
                      </td>

                      <td class="d-flex w-100 p-0" colspan="2">
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
  </script>
  @include('universal-layout.footer')