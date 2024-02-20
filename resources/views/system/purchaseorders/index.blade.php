@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Open Purchase Orders',
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
              <a href="/purchaseorders/create" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-6">
                CREATE A PURCHASE ORDER
              </a>
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title p-0 m-0">Purchase orders</h4>
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
                      <thead class="text-primary">
                        <tr>
                          <th class="p-0 border">
                            Date Created
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
                          @can('accounting')
                          <th class="py-0 px-1 border">
                            Open Balance
                          </th>
                          @endcan
                          <th class="p-0 border">
                            Actions
                          </th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($purchaseOrders as $purchaseOrder)
                        <tr>
                          <td class="p-0">
                            {{ $purchaseOrder->record_date }}
                          </td>

                          <td class="p-0">
                            {{ $purchaseOrder->lpo_prefix }}{{ $purchaseOrder->lpo_suffix }}
                          </td>
                          <td class="p-0">
                            {{ $purchaseOrder->supplier->supplier_name }}
                          </td>
                          <td class="p-0">
                            {{ $purchaseOrder->sum_grandtotal }}
                          </td>
                          @can('accounting')
                          <td class="p-0">
                            {{ number_format($purchaseOrder->amount_due, 2) }}
                          </td>
                          @endcan

                          <td class="d-flex p-0">
                            @can('viewAny', App\Models\PurchaseOrder::class)
                            <a href="/purchaseorders/{{ $purchaseOrder->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm">PRINT</a>
                            @endcan

                            @can('update', [App\Models\PurchaseOrder::class, $purchaseOrder])
                            <a href="/purchaseorders/{{ $purchaseOrder->id }}/complete" type="button" name="button" class="btn btn-info btn-sm">
                              COMPLETE
                            </a>

                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelOrderForm{{ $purchaseOrder->id }}">
                              CANCEL
                            </button>
                            <div class="modal fade" id="cancelOrderForm{{ $purchaseOrder->id }}" tabindex="-1" role="dialog">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                      Cancel
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('purchaseorders.cancel', $purchaseOrder->id ) }}">
                                      @csrf
                                      <input type="hidden" name="purchaseorder_id" value="{{ $purchaseOrder->id }}">

                                      <p class="text-center">
                                        Are you sure you want to cancel this purchase order?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Cancel Purchase Order" class="btn btn-danger">CANCEL</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endcan

                            @if($purchaseOrder->status == App\Enums\PurchaseOrderStatusEnum::CANCELLED)
                            <span class="text-danger ms-2 badge-icon">Cancelled</span>
                            @elseif($purchaseOrder->status == App\Enums\PurchaseOrderStatusEnum::CLOSED)
                            <span class="text-danger ms-2 badge-icon">Closed</span>
                            <a href="/purchaseorders/{{ $purchaseOrder->id }}/complete/show" target="_blank" class="btn btn-sm btn-info">Show</a>
                            @endif

                          </td>
                          <td class="p-0">
                            @can('update', [App\Models\PurchaseOrder::class, $purchaseOrder])
                            <a href="/purchaseorders/{{ $purchaseOrder->id }}/edit" type="button" name="button" class="btn btn-info btn-sm mb-2">
                              EDIT
                            </a>
                            @endcan

                            @can('delete', [App\Models\PurchaseOrder::class, $purchaseOrder])
                            <button type="button" name="button" class="btn btn-danger btn-sm mb-2" data-toggle="modal" data-target="#deleteOrderForm{{ $purchaseOrder->id }}">
                              DELETE
                            </button>

                            <div class="modal fade" id="deleteOrderForm{{ $purchaseOrder->id }}" tabindex="-1" role="dialog">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                      Delete
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('purchaseorders.destroy', $purchaseOrder->id ) }}">
                                      @csrf
                                      @method("DELETE")
                                      <input type="hidden" name="purchaseorder_id" value="{{ $purchaseOrder->id }}">

                                      <p class="text-center">
                                        Are you sure you want to delete this purchase order?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Purchase Order" class="btn btn-danger">YES</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endcan
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="100%">No purchase orders registered</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
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