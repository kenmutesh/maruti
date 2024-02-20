@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Shelf',
'datatable' => 'true',
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
          <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-6" data-toggle="modal" data-target="#createLocationForm">
            <i class="tim-icons icon-simple-add"></i> CREATE A SHELF
          </button>

          <!-- Modal -->
          <div class="modal fade" id="createLocationForm" tabindex="-1" role="dialog" aria-labelledby="createCompanyForm" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Create</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="tim-icons icon-simple-remove"></i>
                  </button>
                </div>
                <div class="modal-body">

                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('shelves.store') }}">
                    @csrf
                    <div class="d-flex flex-column">

                      <div class="col">
                        <div class="form-group">
                          <label for="shelfName">Shelf Name</label>
                          <input type="text" required name="shelf_name" class="form-control" id="shelfName" aria-describedby="shelfName" class="dark-text" placeholder="Enter shelf name">
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="floorID">Floor</label>
                          <select required name="floor_id" class="form-control ms border" id="floorID">
                              @foreach($floors as $floor)
                                <option value="{{ $floor->id }}">
                                  {{ $floor->floor_name }}
                                </option>
                              @endforeach
                          </select>
                        </div>
                      </div>

                    </div>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                  <button type="submit" name="submit_btn" value="Create Shelf" class="btn btn-primary">CREATE SHELF</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title m-0 p-0">Available Shelves</h4>
            </div>
            <div class="card-body">
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
                        Shelf Name
                      </th>
                      <th class="py-0 px-1 border">
                        Floor
                      </th>
                      <th class="py-0 px-1 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($shelves as $shelf)
                        <tr>
                          <td class="p-0">
                            {{ $shelf->created_at }}
                          </td>
                          <td class="p-0">
                            {{ $shelf->shelf_name }}
                          </td>

                          <td class="p-0">
                            {{ $shelf->floor->floor_name }}
                          </td>

                          <td class="p-0">
                            <button type="button" name="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editShelfForm{{ $shelf->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Edit Shelf">
                                <i class="material-icons">mode_edit</i>
                              </span>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="editShelfForm{{ $shelf->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Floor</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('shelves.update', $shelf->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <input type="hidden" name="shelf_id" value="{{ $shelf->id }}">

                                      <div class="d-flex flex-column">

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="shelfName">Shelf Name</label>
                                            <input type="text" required name="shelf_name" class="form-control" id="shelfName" aria-describedby="shelfName" class="dark-text" placeholder="Enter shelf name" value="{{ $shelf->shelf_name }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="floorID">Floor Name</label>
                                            <select required name="floor_id" class="form-control ms border" id="floorID">
                                                @foreach($floors as $floor)
                                                  @if($floor->id == $shelf->floor->id)
                                                    <option value="{{ $floor->id }}" selected>
                                                      {{ $floor->floor_name }}  (CURRENT)
                                                    </option>
                                                  @else
                                                    <option value="{{ $floor->id }}">
                                                      {{ $floor->floor_name }}
                                                    </option>
                                                  @endif
                                                @endforeach
                                            </select>
                                          </div>
                                        </div>

                                      </div>

                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Edit Shelf" class="btn btn-primary">SUBMIT EDITS</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->
                          
                            <button type="button" name="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteShelfForm{{ $shelf->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Delete Shelf">
                                <i class="material-icons">delete</i>
                              </span>
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="deleteShelfForm{{ $shelf->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Shelf: {{ $shelf->shelf_name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('shelves.destroy', $shelf->id ) }}">
                                      @csrf
                                      @method("DELETE")
                                      <input type="hidden" name="shelf_id" value="{{ $shelf->id }}">

                                      <p class="text-center">
                                        Are you sure you want to delete this shelf?
                                      </p>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Delete Shelf" class="btn btn-danger">YES</button>
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
