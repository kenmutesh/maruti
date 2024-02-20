
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Bin',
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
          <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 col-sm-6" data-toggle="modal" data-target="#creatBinForm">
            <i class="tim-icons icon-simple-add"></i> CREATE A BIN
          </button>

          <!-- Modal -->
          <div class="modal fade" id="creatBinForm" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Create</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="tim-icons icon-simple-remove"></i>
                  </button>
                </div>
                <div class="modal-body">

                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('bins.store') }}">
                    @csrf
                    <div class="d-flex flex-column">

                      <div class="col">
                        <div class="form-group">
                          <label for="shelfID">Shelf Name</label>
                          <select required name="shelf_id" class="form-control ms border" id="shelfID">
                              @foreach($shelves as $shelf)
                                <option value="{{ $shelf->id }}">
                                  {{ $shelf->shelf_name }}
                                </option>
                              @endforeach
                          </select>
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="binName">Bin Name</label>
                          <input type="text" required name="bin_name" class="form-control" id="binName" aria-describedby="binName" class="dark-text" placeholder="Enter bin name">
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="binDescription">Bin Description</label>
                          <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="bin_description" class="form-control" rows="3" required id="binDescription"></textarea>
                        </div>
                      </div>

                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                  <button type="submit" name="submit_btn" value="Create Bin" class="btn btn-primary">CREATE</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">Available Bins</h4>
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
                        Name - Shelf
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
                    @foreach($bins as $singleBin)
                      <tr>
                        <td class="p-0">
                          {{ $singleBin->created_at }}
                        </td>

                        <td class="p-0">
                          <span>{{ $singleBin->bin_name }}</span> -
                          <span>{{ $singleBin->shelf->shelf_name }}</span>
                        </td>

                        <td class="p-0">
                          {{ $singleBin->bin_description }}
                        </td>

                        <td class="p-0">
                          <button type="button" name="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editBinForm{{ $singleBin->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Edit Bin">
                                <i class="material-icons">mode_edit</i>
                              </span>
                          </button>
                          <!-- Modal -->
                          <div class="modal fade" id="editBinForm{{ $singleBin->id }}" tabindex="-1" role="dialog" aria-labelledby="editWarehouseForm{{ $singleBin->id }}" aria-hidden="true">
                            <div class="modal-dialog"  role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Edit Bin</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <i class="tim-icons icon-simple-remove"></i>
                                  </button>
                                </div>
                                <div class="modal-body">

                                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('bins.update', $singleBin->id) }}">
                                    @csrf
                                    @method("PUT")
                                    <input type="hidden" name="bin_id" value="{{ $singleBin->id }}">

                                    <div class="d-flex flex-column">

                                      <div class="col">
                                        <div class="form-group">
                                          <label for="shelfID">Shelf Name</label>
                                          <select required name="shelf_id" class="form-control ms border" id="shelfID">
                                              @foreach($shelves as $singleShelf)
                                                @if($singleShelf->id == $singleBin->shelf->id)
                                                  <option value="{{ $singleShelf->id }}" selected>
                                                    {{ $singleShelf->shelf_name }} ( {{ $singleShelf->floor->floor_name }}) (CURRENT)
                                                  </option>
                                                @else
                                                  <option value="{{ $singleShelf->id }}">
                                                    {{ $singleShelf->shelf_name }} ({{ $singleShelf->floor->floor_name }})
                                                  </option>
                                                @endif
                                              @endforeach
                                          </select>
                                        </div>
                                      </div>

                                      <div class="col">
                                        <div class="form-group">
                                          <label for="binName">Bin Name</label>
                                          <input type="text" required name="bin_name" class="form-control" id="binName" aria-describedby="binName" class="dark-text" placeholder="Enter bin name" value="{{ $singleBin->bin_name }}">
                                        </div>
                                      </div>

                                      <div class="col">
                                        <div class="form-group">
                                          <label for="binDescription">Bin Description</label>
                                          <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="bin_description" class="form-control" rows="3" required id="binDescription">{{ $singleBin->bin_description }}</textarea>
                                        </div>
                                      </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                  <button type="submit" name="submit_btn" value="Edit Bin" class="btn btn-primary">SUBMIT EDITS</button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- end modal -->
                        
                          <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteBinForm{{ $singleBin->id }}">
                              <span class="d-block w-100 h-100" data-toggle="tooltip" title="Delete Bin">
                                <i class="material-icons">delete</i>
                              </span>
                          </button>
                          <!-- Modal -->
                          <div class="modal fade" id="deleteBinForm{{ $singleBin->id }}" tabindex="-1" role="dialog" aria-labelledby="editBinForm{{ $singleBin->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Delete Bin: {{ $singleBin->bin_name }}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <i class="tim-icons icon-simple-remove"></i>
                                  </button>
                                </div>
                                <div class="modal-body">

                                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('bins.destroy', $singleBin->id ) }}">
                                    @csrf
                                    @method("DELETE")
                                    <input type="hidden" name="bin_id" value="{{ $singleBin->id }}">

                                    <p class="text-center">
                                      Are you sure you want to delete this location?
                                    </p>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                  <button type="submit" name="submit_btn" value="Delete Bin" class="btn btn-danger">YES</button>
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
