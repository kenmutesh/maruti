@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | View Credit Notes',
'bootstrapselect' => true,
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
  'slug' => '/purchases'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">
            <div class="row">

              <div class="col-md-12">
                <div class="card ">
                  <div class="card-header">
                    <h4 class="card-title p-0 m-0">Credit Notes</h4>
                  </div>
                  <div class="card-body p-0">
                    <div class="py-3 row m-0">
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
                        <thead class=" text-primary">
                          <tr>
                            <th class="p-0 border">
                              Date Created
                            </th>
                            <th class="p-0 border">
                              Suppliers
                            </th>
                            <th class="p-0 border">
                              Grand Total
                            </th>
                            <th class="p-0 border">
                              Purchase Order
                            </th>
                            <th class="p-0 border">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($creditNotes as $creditNote)
                          <tr>
                            <td class="p-0">
                              {{ $creditNote->created_at }}
                            </td>
                            <td class="p-0">
                              {{ $creditNote->supplier->supplier_name }}
                            </td>
                            <td class="p-0">
                              {{ number_format($creditNote->sum_grandtotal, 2) }}
                            </td>
                            <td class="p-0">
                              @if($creditNote->purchase_order_id)
                                <span>{{ $creditNote->purchaseorder->lpo_prefix }}{{ $creditNote->purchaseorder->lpo_suffix }}</span>
                              @endif
                            </td>
                            <td class="p-0">
                              <a href="/suppliercreditnotes/{{ $creditNote->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">
                                VIEW DOC
                              </a>
                              @if($creditNote->cancelled_at)
                              <span class="text-danger">Cancelled</span>
                              @else
                                <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelForm{{ $creditNote->id }}">
                                  <span class="d-block w-100 h-100" data-toggle="tooltip" title="CANCEL">
                                    CANCEL
                                  </span>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="cancelForm{{ $creditNote->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Cancel</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                          <i class="tim-icons icon-simple-remove"></i>
                                        </button>
                                      </div>
                                      <div class="modal-body">

                                        <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('suppliercreditnotes.destroy', $creditNote->id ) }}">
                                          @csrf
                                          @method("DELETE")

                                          <p class="text-center">
                                            Are you sure you want to cancel?
                                          </p>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                            <button type="submit" name="submit_btn" value="Delete" class="btn btn-danger">YES</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
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