
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Transfers',
'datatable' => true,
]
)
<style>
  .table .form-check label .form-check-sign:before,
  .table .form-check label .form-check-sign:after {
    top: 0px;
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
  'slug' => '/inventory'
  ]
  )

  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">

      <div class="content">

        <div class="row">
          <a href="/transfers/create" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-5">
            <i class="tim-icons icon-simple-add"></i> CREATE A TRANSFER
          </a>

        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">Available Transfers</h4>
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
              <div class="table-responsive p-0">
                <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="open-list">
                  <thead class="text-primary">
                    <tr>
                      <th class="p-0 border">
                        Date Created
                      </th>
                      <th class="p-0 border">
                        Requested By
                      </th>
                      <th class="p-0 border">
                        Approval Status
                      </th>
                      <th class="p-0 border">
                        Approved/Rejected By
                      </th>
                      <th class="p-0 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($transfers as $singleTransfer)
                        <tr>
                          <td class="p-0">
                            {{ $singleTransfer->date_created }}
                          </td>

                          <td class="p-0">
                            {{ $singleTransfer->requester->username }}
                          </td>

                          <td class="p-0">
                            @if($singleTransfer->approved)
                              @if($singleTransfer->approved == 1)
                                <span class="badge badge-success">APPROVED</span>
                              @elseif($singleTransfer->approved == 2)
                                <span class="badge badge-danger">REJECTED</span>
                              @endif
                            @else
                              <span class="badge badge-default mb-1">PENDING APPROVAL</span> <br>
                              @if(session()->get('auth_warehouse_uid') == 'N/A')
                                <a href="/transfers/approve/{{ $singleTransfer->id }}" class="btn btn-success p-2">
                                  APPROVE
                                </a>
                                <a href="/transfers/reject/{{ $singleTransfer->id }}" class="btn btn-danger p-2">
                                  REJECT
                                </a>
                              @endif
                            @endif
                          </td>

                          <td class="p-0">
                            @if($singleTransfer->approvedby)
                            {{ $singleTransfer->approvedby->username }}
                            @else
                            -
                            @endif
                          </td>

                          <td class="p-0">
                            @if($singleTransfer->approved != 2)
                            <a href="/transfers/viewdoc/{{ $singleTransfer->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm" >PRINT</a>
                            @endif
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
