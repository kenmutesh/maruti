@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | AR Aging Report',
'datatable' => true,
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
        <div class="main-panel">

          <div class="content ml-sm-4">
            <div class="row">

              <div class="col-md-12">
                <div class="card ">
                    <div class="d-flex mb-2 justify-content-between">
  
                      <div class="col-sm-5 d-flex p-0 px-1 justify-content-around align-items-center">
                          <p class="m-0">Date</p>
                          @if($date)
                            <input type="date" class="rounded-sm border border-dark p-1 col ml-1" onchange="window.location.href='?date=' + this.value" value="{{ $date }}">
                          @else
                            <input type="date" class="rounded-sm borcder border-dark p-1 col ml-1" onchange="window.location.href='?date=' + this.value">
                          @endif
                      </div>
  
                      <div class="col-sm-5 d-flex p-0 px-1 justify-content-around align-items-center">
                          <p class="m-0">Sort By:</p>
                          <select onchange="sortTable(this)" class="ms form-control border border-dark rounded-sm col ml-1">
                            <option value="">Default</option>
                            <option value="name">Name</option>
                            <option value="current">Current</option>
                          </select>
                      </div>
  
                    </div>
                  <div class="card-header text-center p-0">
                    @if($date)
                    <p class="card-title p-0 m-0 font-weight-bold">A\R Aging Report As of: {{ date('d/m/Y', strtotime($date)) }}</p>
                    @else
                    <p class="card-title p-0 m-0 font-weight-bold">A\R Aging Report As of: {{ date('d/m/Y', time()) }}</p>
                    @endif
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive p-0">
                      <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table">
                        <thead class=" text-primary">
                          <tr>
                            <th class="p-0">
                              Customer Name
                            </th>
                            <th class="p-0">
                              Current
                            </th>
                            <th class="p-0">
                              1 - 30
                            </th>
                            <th class="p-0">
                              31 - 60
                            </th>
                            <th class="p-0">
                              61 - 90
                            </th>
                            <th class="p-0">
                              More than 90
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($customers as $customer)
                          <tr>
                            <td class="p-0">{{ $customer->customer_name }}</td>
                            <td class="p-0">
                              {{ number_format($customer->current_balance ,2) }}
                            </td>
                            <td class="p-0">
                              {{ number_format($customer->thirty_day_balance ,2) }}
                            </td>
                            <td class="p-0">
                              {{ number_format($customer->sixty_day_balance ,2) }}
                            </td>
                            <td class="p-0">
                              {{ number_format($customer->ninety_day_balance ,2) }}
                            </td>
                            <td class="p-0">
                              {{ number_format($customer->over_ninety_day_balance,2) }}
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

  <script src="/assets/js/plugins/dataTables.buttons.min.js"></script>
  <script src="/assets/js/plugins/jszip.min.js"></script>
  <script src="/assets/js/plugins/pdfmake.min.js"></script>
  <script src="/assets/js/plugins/vfs_font.js"></script>
  <script src="/assets/js/plugins/buttons.html5.min.js"></script>
  <script src="/assets/js/plugins/buttons.print.min.js"></script>

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
          dom: 'Bfrtip',
          buttons: [
            'pdf', 'print'
          ]
        });

        dataTableInstances.push(table);
        const printers = document.querySelectorAll('.dt-buttons button');
        printers.forEach((printerBtn) => {
          printerBtn.classList.add('btn', 'btn-warning');
        })
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

    function resetOptions(input) {
      dataTableInstances[0].search(input.value).draw();
    }

    function sortTable(selectElement) {
      dataTableInstances[0].order([selectElement.selectedIndex-1, 'asc']).draw()
    }
  </script>
  @include('universal-layout.footer')