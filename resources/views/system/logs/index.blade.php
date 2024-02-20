
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | System Logs',
'datatable' => true,
]
)
<style>
  .modal-backdrop.show {
    z-index: 4;
  }
  .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/logs'
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
              <h4 class="card-title p-0 m-0">Available Logs</h4>
            </div>
            <div class="card-body p-0">
            <div class="py-3 row m-0">
                    <div class="d-flex flex-column col-sm-6 invisible">
                      Pages: 
                    </div>
                    <div class="d-flex justify-content-around col-sm-6 p-0 logs-list filters">
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
              <div class="table-responsive p-0">
                <table class="table table-bordered tablesorter data-table" id="" data-table="logs-list">
                  <thead class="text-primary">
                    <tr>
                      <th class="p-0 border">
                        Date
                      </th>
                      <th class="p-0 border" style="width:4rem;">
                        Action
                      </th>
                      <th class="p-0 border">
                        Username
                      </th>
                      <th class="p-0 border">
                        Email
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($logs as $singleLog)
                        <tr>
                          <td class="d-flex flex-column text-left p-0">
                            {{ $singleLog->date_created }}
                          </td>

                          <td class="text-truncate p-0">
                            <span class="text-truncate" style="width:4rem;" data-toggle="tooltip" title="{!! htmlspecialchars_decode($singleLog->action) !!}">
                              {!! htmlspecialchars_decode($singleLog->action) !!}
                            </span>
                          </td>

                          <td class="p-0">
                            {{ $singleLog->systemuser->username ?? '' }}
                          </td>
                          <td class="p-0">
                            {{ $singleLog->systemuser->email ?? '' }}
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

      @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'datatable' =>true,
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
