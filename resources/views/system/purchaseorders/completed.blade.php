
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Completed Purchase Orders',
'bootstrapselect' => true,
'datatable' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .bootstrap-select {
        padding: 0 !important;
    }
</style>

<body class="theme-green">
    @include('universal-layout.spinner')

    @include('universal-layout.system-sidemenu',
    [
    'slug' => '/purchases'
    ]
    )
    <section class="content home">
        <div class="container-fluid">
            <div class="wrapper">
                <div class="main-panel">

      <div class="content">

        <div class="row">
          <a href="/purchaseorders/create" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 w-25">
            <i class="tim-icons icon-simple-add"></i> CREATE A PURCHASE ORDER
          </a>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title m-0 p-0">Completed purchases</h4>
            </div>
            <div class="card-body p-0">
            <div class="py-3 row m-0">
                    <div class="d-flex flex-column col-sm-6 invisible">
                      Pages: 
                    </div>
                    <div class="d-flex justify-content-around col-sm-6 p-0 purchase-list filters">
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
                <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="purchase-list">
                  <thead class="text-primary">
                    <tr>
                      <th class="p-0 border">
                        Date
                      </th>
                      <th class="p-0 border">
                        PO
                      </th>
                      <th class="p-0 border">
                        Supplier
                      </th>
                      <th class="p-0 border">
                        Grand Total
                      </th>
                      <th class="p-0 border">
                        Actions
                      </th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($openPurchaseOrders as $singlePurchaseOrder)
                      <tr>
                        <td class="p-0">
                          {{ $singlePurchaseOrder->record_date }}
                        </td>

                        <td class="text-nowrap p-0">
                            {{ $singlePurchaseOrder->lpo_prefix }}{{ $singlePurchaseOrder->lpo_suffix }}
                        </td>

                        <td class="p-0">
                          {{ $singlePurchaseOrder->supplier->supplier_name }}
                        </td>

                        <td class="p-0">
                          {{ $singlePurchaseOrder->grand_total }}
                        </td>


                        <td class="p-0">
                          <a href="/purchaseorders/completed/{{ $singlePurchaseOrder->id }}" type="button" name="button" class="btn btn-info btn-sm">
                              VIEW COMPLETION DETAILS
                          </a>

                        </td>
                        <td class="p-0">
                          <a href="/purchaseorders/viewdoc/{{ $singlePurchaseOrder->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                            PRINT
                          </a>
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
    </section>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

      <script type="text/javascript">

      async function printInfo(button, filename) {
        const worker = html2pdf();
        await worker.from(button.parentElement.nextElementSibling);

        let myPdf = await worker.save(filename);
      }
      </script>
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