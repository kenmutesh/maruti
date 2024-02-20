@include('universal-layout.header', ['pageTitle' => 'Aprotec | VAT'])
<style>
  .modal-backdrop.show {
    z-index: 4;
  }
</style>
<body class="theme-green">
  @include('universal-layout.spinner')

@include('universal-layout.system-sidemenu',
[
'slug' => '/settings'
]
)
<section class="content home">
    <div class="container-fluid">
  <div class="wrapper">
    <div class="main-panel">

      <div class="content">
        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">TAX</h4>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table">
                  <thead class="text-primary">
                    <tr>
                      <th class="py-0 px-1 border">
                        Date Created
                      </th>
                      <th class="py-0 px-1 border">
                        Type
                      </th>
                      <th class="py-0 px-1 border">
                        Percentage
                      </th>
                      <th class="py-0 px-1 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($taxes as $tax)
                        <tr>
                          <td class="p-0">
                            {{ $tax->created_at }}
                          </td>

                          <td class="p-0">
                            {{ $tax->type->humanreadablestring() }}
                          </td>

                          <td class="p-0">
                            {{ $tax->percentage }}%
                          </td>

                          <td class="p-0">
                            <button type="button" name="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editEntityForm{{ $tax->id }}">
                              EDIT VAT
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" style="z-index: 5;" id="editEntityForm{{ $tax->id }}" tabindex="-1" role="dialog" aria-labelledby="editEntityForm{{ $tax->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit VAT</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('taxes.update', $tax->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <input type="hidden" name="id" value="{{ $tax->id }}">

                                      <div class="d-flex flex-column">

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="alphabeticalSection">Percentage</label>
                                            <input type="number" max="100" step=".01" name="percentage" class="form-control" id="alphabeticalSection" aria-describedby="alphabericalSection" class="dark-text" required placeholder="Enter VAT percentage" value="{{ $tax->percentage }}">
                                          </div>
                                        </div>

                                      </div>

                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Edit VAT" class="btn btn-primary">SUBMIT EDITS</button>
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
  </div></div></section>

@include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
   
  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')
