@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Accounts Suppliers',
'datatable' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
  }
</style>

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

      <div class="content">

        <div class="row">
          <a href="/expenses/bills/create" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 w-25">
            <i class="tim-icons icon-simple-add"></i> CREATE A BILL PAYMENT
          </a>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">Available Payments</h4>
            </div>
            <div class="card-body p-0">
            <div class="py-3 row m-0">
                    <div class="d-flex justify-content-around col-sm-6 p-0 payment-list filters">
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
                <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="payment-list">
                  <thead class="text-primary">
                    <tr>
                      <th class="p-0">
                        Date Created
                      </th>
                      <th class="p-0">
                        Payment Mode
                      </th>
                      <th class="p-0">
                        Transaction Reference
                      </th>
                      <th class="p-0">
                        Amount Paid
                      </th>
                      <th class="p-0">
                        Supplier Name
                      </th>
                      <th class="p-0">
                        PO Referenced
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($bills as $singleBill)
                        <tr>
                          <td class="p-0">
                            {{ $singleBill->date_created }}
                          </td>

                          <td class="p-0">
                            {{ $singleBill->payment_mode }}
                          </td>

                          <td class="text-center p-0">
                            {{ $singleBill->transaction_ref }}
                          </td>

                          <td class="p-0">
                            {{ $singleBill->amount}}
                          </td>

                          <td class="p-0">
                            {{ $singleBill->supplier->supplier_name }}
                          </td>
                          <td class="p-0">
                          <a href="/purchaseorders/viewdoc/{{ $singleBill->referenced_po }}" target="_blank" type="button" name="button" class="btn btn-info mb-2" >
                                     <i class="tim-icons icon-paper"></i>VIEW PO
                          </a>
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

