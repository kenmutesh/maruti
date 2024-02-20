
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Memos',
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


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">Available Memos</h4>
            </div>
            <div class="card-body p-0">
            <div class="py-3 row m-0">
                  <div class="d-flex flex-column col-sm-6 invisible">
                    Pages: 
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
                      <th class="p-0">
                        Date Created
                      </th>
                      <th class="p-0">
                        Responsible
                      </th>
                      <th class="p-0">
                        Warehouse
                      </th>
                      <th class="p-0">
                        Bin
                      </th>
                      <th class="p-0">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($memos as $singleMemo)
                        <tr>
                          <td class="p-0">
                            {{ $singleMemo->date_created }}
                          </td>

                          <td class="d-flex flex-column text-left p-0">
                            {{ $singleMemo->responsibleperson->username }}
                          </td>

                          <td class="p-0">
                            {{ $singleMemo->warehouse->warehouse_name }}
                          </td>

                          <td class="p-0">
                            {{ $singleMemo->bin->bin_name }}
                          </td>

                          <td class="p-0">
                            <a href="/inventory/memo/viewdoc/{{ $singleMemo->id }}" target="_blank" type="button" name="button" class="btn btn-info" >PRINT</a>
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
  </script>
  @include('universal-layout.footer')
