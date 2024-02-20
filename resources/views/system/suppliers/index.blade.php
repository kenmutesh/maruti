@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Suppliers',
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
  'slug' => '/suppliers'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">
            <div class="row">
              @can('create', App\Models\Supplier::class)
              <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-6" data-toggle="modal" data-target="#createSupplierForm">
                <i class="tim-icons icon-simple-add"></i> CREATE A SUPPLIER
              </button>

              <div class="modal fade" id="createSupplierForm" tabindex="-1" role="dialog" aria-labelledby="createSupplierForm" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Create A Supplier in the System</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="tim-icons icon-simple-remove"></i>
                      </button>
                    </div>
                    <div class="modal-body">

                      <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('suppliers.store') }}">
                        @csrf
                        <div class="d-flex flex-column">

                          <div class="row">
                            <div class="col">
                              <div class="form-group">
                                <label for="supplierName">Supplier Name</label>
                                <input type="text" required name="supplier_name" class="form-control" id="supplierName" aria-describedby="supplierName" class="dark-text" placeholder="Enter supplier name">
                              </div>
                            </div>

                            <div class="col">
                              <div class="form-group">
                                <label for="supplierEmail">Supplier Email</label>
                                <input type="text" required name="supplier_email" class="form-control" id="supplierEmail" aria-describedby="supplierEmail" class="dark-text" placeholder="Enter supplier email">
                              </div>
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="supplierMobile">Supplier Mobile</label>
                              <input type="text" required name="supplier_mobile" class="form-control" id="supplierMobile" aria-describedby="supplierMobile" class="dark-text" placeholder="Enter supplier mobile">
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="companyLocation">Company Location</label>
                              <input type="text" required name="company_location" class="form-control" id="companyLocation" aria-describedby="companyLocation" class="dark-text" placeholder="Enter company location">
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="companyPIN">Company PIN Number</label>
                              <input type="text" required name="company_pin" class="form-control" id="companyPIN" aria-describedby="companyPIN" class="dark-text" placeholder="Enter company location">
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="companyBox">Company PO Box</label>
                              <input type="text" required name="company_box" class="form-control" id="companyBox" aria-describedby="companyBox" class="dark-text" placeholder="Enter company box">
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="companyBox">Opening Balance</label>
                              <input type="number" step="any" required name="opening_balance" class="form-control" id="companyBox" aria-describedby="companyBox" class="dark-text" placeholder="Enter opening balance">
                            </div>
                          </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                      <button type="submit" name="submit_btn" value="Create Supplier" class="btn btn-primary">CREATE SUPPLIER</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              @endcan
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title p-0 m-0">Available Suppliers</h4>
                </div>
                <div class="card-body p-0">
                  <div class="py-3 row m-0">
                    <div class="d-flex justify-content-around col-sm-6 p-0 location-list filters">
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
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" data-table="location-list">
                      <thead class="text-primary">
                        <tr>
                          <th class="py-0 px-1 border">
                            Date Created
                          </th>
                          <th class="py-0 px-1 border">
                            Name
                          </th>
                          <th class="py-0 px-1 border">
                            Location
                          </th>
                          <th class="py-0 px-1 border">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($suppliers as $supplier)
                        <tr>
                          <td class="p-0">
                            {{ $supplier->created_at }}
                          </td>

                          <td class="p-0">
                            {{ $supplier->supplier_name }}
                          </td>

                          <td class="p-0 text-left">
                            {{ $supplier->company_location }}
                          </td>

                          <td class="p-0">
                            @can('update', App\Models\Supplier::class)
                            <button type="button" name="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editSupplierForm{{ $supplier->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Edit Supplier">
                                <i class="material-icons">mode_edit</i>
                              </span>
                            </button>

                            <div class="modal fade" id="editSupplierForm{{ $supplier->id }}" tabindex="-1" role="dialog" aria-labelledby="editSupplierForm{{ $supplier->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Supplier</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('suppliers.update', $supplier->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                                      <div class="d-flex flex-column">

                                        <div class="row">
                                          <div class="col">
                                            <div class="form-group">
                                              <label for="supplierName">Supplier Name</label>
                                              <input type="text" required name="supplier_name" class="form-control" id="supplierName" aria-describedby="supplierName" class="dark-text" placeholder="Enter supplier name" value="{{ $supplier->supplier_name }}">
                                            </div>
                                          </div>

                                          <div class="col">
                                            <div class="form-group">
                                              <label for="supplierEmail">Supplier Email</label>
                                              <input type="text" required name="supplier_email" class="form-control" id="supplierEmail" aria-describedby="supplierEmail" class="dark-text" placeholder="Enter supplier email" value="{{ $supplier->supplier_email }}">
                                            </div>
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="supplierMobile">Supplier Mobile</label>
                                            <input type="text" required name="supplier_mobile" class="form-control" id="supplierMobile" aria-describedby="supplierMobile" class="dark-text" placeholder="Enter supplier mobile" value="{{ $supplier->supplier_mobile }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="companyLocation">Company Location</label>
                                            <input type="text" required name="company_location" class="form-control" id="companyLocation" aria-describedby="companyLocation" class="dark-text" placeholder="Enter company location" value="{{ $supplier->company_location }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="companyPIN">Company PIN Number</label>
                                            <input type="text" required name="company_pin" class="form-control" id="companyPIN" aria-describedby="companyPIN" class="dark-text" placeholder="Enter company location" value="{{ $supplier->company_pin }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="companyBox">Company PO Box</label>
                                            <input type="text" required name="company_box" class="form-control" id="companyBox" aria-describedby="companyBox" class="dark-text" placeholder="Enter company box" value="{{ $supplier->company_box }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="companyBox">Opening Balance</label>
                                            <input type="number" step="any" required name="opening_balance" class="form-control" id="companyBox" aria-describedby="companyBox" class="dark-text" placeholder="Enter opening balance" value="{{ $supplier->opening_balance }}">
                                          </div>
                                        </div>

                                      </div>

                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Edit Supplier" class="btn btn-primary">SUBMIT EDITS</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endcan

                            @can('delete', App\Models\Supplier::class)
                            <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSupplierForm{{ $supplier->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Delete Supplier">
                                <i class="material-icons">delete</i>
                              </span>
                            </button>

                            <div class="modal fade" id="deleteSupplierForm{{ $supplier->id }}" tabindex="-1" role="dialog" aria-labelledby="editSupplierForm{{ $supplier->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Supplier: {{ $supplier->supplier_name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('suppliers.destroy', $supplier->id ) }}">
                                      @csrf
                                      @method("DELETE")
                                      <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                                      <p class="text-center">
                                        Are you sure you want to delete this location?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Supplier" class="btn btn-danger">YES</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
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