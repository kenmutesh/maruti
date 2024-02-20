@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Warehouse',
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
  'slug' => '/locations'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">

            <div class="row">
              <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-6" data-toggle="modal" data-target="#creatWarehouseForm">
                <i class="tim-icons icon-simple-add"></i> CREATE A WAREHOUSE
              </button>

              <!-- Modal -->
              <div class="modal fade" id="creatWarehouseForm" style="z-index: 5;" tabindex="-1" role="dialog" aria-labelledby="createWarehouseForm" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Create</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="tim-icons icon-simple-remove"></i>
                      </button>
                    </div>
                    <div class="modal-body">

                      <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('warehouses.store') }}">
                        @csrf
                        <div class="d-flex flex-column">

                          <div class="col">
                            <div class="form-group">
                              <label for="locationID">Location Name</label>
                              <select required name="location_id" class="form-control ms" id="locationID">
                                @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="warehouseName">Warehouse Name</label>
                              <input type="text" required name="warehouse_name" class="form-control" id="warehouseName" aria-describedby="warehouseName" class="dark-text" placeholder="Enter warehouse name">
                            </div>
                          </div>

                          <div class="col">
                            <div class="form-group">
                              <label for="warehouseDescription">Warehouse Description</label>
                              <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="warehouse_description" class="form-control" rows="3" required id="warehouseDescription"></textarea>
                            </div>
                          </div>

                        </div>

                    </div>
                    <div class="modal-footer d-flex">
                      <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">CLOSE</button>
                      <button type="submit" name="submit_btn" value="Create Warehouse" class="btn btn-success col-6">CREATE</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="col">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="card-title m-0 p-0">Available Warehouses</h4>
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
                            Name - Location
                          </th>
                          <th class="py-0 px-1 border">
                            Description
                          </th>
                          <th class="py-0 px-1 border">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($warehouses as $warehouse)
                        <tr>
                          <td class="p-0">
                            {{ $warehouse->created_at }}
                          </td>

                          <td class="p-0">
                            <span>{{ $warehouse->warehouse_name }}</span> -
                            <span>{{ $warehouse->location->location_name }}</span>

                          </td>

                          <td class="p-0" class="p-0 text-truncate overflow-hidden" style="max-width: 5rem;" title="{{ $warehouse->warehouse_description }}">
                            {{ $warehouse->warehouse_description }}
                          </td>

                          <td class="p-0">
                            <button type="button" name="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editWarehouseForm{{ $warehouse->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Edit Warehouse">
                                <i class="material-icons">mode_edit</i>
                              </span>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="editWarehouseForm{{ $warehouse->id }}" tabindex="-1" role="dialog" aria-labelledby="editWarehouseForm{{ $warehouse->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Warehouse</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('warehouses.update', $warehouse->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">

                                      <div class="d-flex flex-column">

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="locationID">Location Name</label>
                                            <select required name="location_id" class="form-control ms" id="locationID">
                                              @foreach($locations as $singleLocation)
                                              @if($singleLocation->id == $warehouse->location->id)
                                              <option value="{{ $singleLocation->id }}" selected>{{ $singleLocation->location_name }} (CURRENT)</option>
                                              @else
                                              <option value="{{ $singleLocation->id }}">{{ $singleLocation->location_name }}</option>
                                              @endif
                                              @endforeach
                                            </select>
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="warehouseName">Warehouse Name</label>
                                            <input type="text" required name="warehouse_name" class="form-control" id="warehouseName" aria-describedby="warehouseName" class="dark-text" placeholder="Enter warehouse name" value="{{ $warehouse->warehouse_name }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="warehouseDescription">Warehouse Description</label>
                                            <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="warehouse_description" class="form-control" rows="3" required id="warehouseDescription">{{ $warehouse->warehouse_description }}</textarea>
                                          </div>
                                        </div>

                                      </div>
                                  </div>
                                  <div class="modal-footer d-flex">
                                    <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Create Company" class="btn btn-success col-6">SUBMIT EDITS</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->
                          
                            <button type="button" name="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteLocationForm{{ $singleLocation->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Delete Warehouse">
                                <i class="material-icons">delete</i>
                              </span>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="deleteLocationForm{{ $singleLocation->id }}" tabindex="-1" role="dialog" aria-labelledby="editLocationForm{{ $singleLocation->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Warehouse: {{ $warehouse->warehouse_name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('warehouses.destroy', $warehouse->id ) }}">
                                      @csrf
                                      @method("DELETE")
                                      <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">

                                      <p class="text-center">
                                        Are you sure you want to delete this warehouse?
                                      </p>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                        <button type="submit" name="submit_btn" value="Delete Location" class="btn btn-danger">YES</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->
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