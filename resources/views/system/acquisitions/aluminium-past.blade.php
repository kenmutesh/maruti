
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Past Aluminium Inventory Acquisitions',
'datatable' => true,
]
)

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/acquisitions'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

      <div class="content">

        <div class="row">
          <a href="/acquisition/aluminium" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-3">
            <i class="tim-icons icon-simple-add"></i> CREATE NEW
          </a>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title m-0 p-0">Aluminium inventory acquisition</h4>
            </div>
            <div class="card-body p-0">
            <div class="py-3 row m-0">
                    <div class="d-flex flex-column col-sm-6 invisible">
                      Pages: 
                    </div>
                    <div class="d-flex justify-content-around col-sm-6 p-0 acquisition-list filters">
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
                <table class="table table-bordered tablesorter data-table fixed-table" data-table="acquisition-list">
                  <thead class="text-primary">
                    <tr>
                      <th class="p-0 border">
                        Date of action
                      </th>
                      <th class="p-0 border">
                        Done By
                      </th>
                      <th class="p-0 border">
                        Warehouse
                      </th>
                      <th class="p-0 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($aluminiumAcquisitions as $singleAcquisition)
                      <tr>
                        <td class="p-0">
                          {{ $singleAcquisition->date_created }}
                        </td>
                        <td class="p-0">
                          {{ $singleAcquisition->systemuser->username }}
                        </td>

                        <td class="p-0">
                          {{ $singleAcquisition->warehouse->warehouse_name }}
                        </td>

                        <td class="p-0">
                          <a href="/acquisition/viewdoc/{{ $singleAcquisition->id }}" target="_blank" type="button" name="button" class="btn btn-sm btn-info" >PRINT</a>
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
  </script>
  @include('universal-layout.footer')