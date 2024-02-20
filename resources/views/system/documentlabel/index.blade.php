@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Document Labels',
]
)
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
              <h4 class="card-title p-0 m-0">Available Document Labels</h4>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table">
                  <thead class="text-primary">
                    <tr>
                      <th class="py-0 px-1 border">
                        Document
                      </th>
                      <th class="py-0 px-1 border">
                        Alphabetical Section
                      </th>
                      <th class="py-0 px-1 border">
                        Numerical Section
                      </th>
                      <th class="py-0 px-1 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($documentLabels as $singleDocumentLabel)
                        <tr>
                          <td class="p-0">
                            {{ $singleDocumentLabel->document->humanreadablestring() }}
                          </td>

                          <td class="text-center p-0">
                            {{ $singleDocumentLabel->document_prefix }}
                          </td>

                          <td class="p-0">
                            {{ $singleDocumentLabel->document_suffix }}
                          </td>

                          <td class="p-0">
                            <button type="button" name="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editEntityForm{{ $singleDocumentLabel->id }}">
                              EDIT LABEL
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="editEntityForm{{ $singleDocumentLabel->id }}" tabindex="-1" role="dialog" aria-labelledby="editEntityForm{{ $singleDocumentLabel->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Document Label</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{  route('documentlabels.update', $singleDocumentLabel->id) }}">
                                      @csrf
                                      @method("PUT")
                                      <input type="hidden" name="id" value="{{ $singleDocumentLabel->id }}">

                                      <div class="d-flex flex-column">

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="alphabeticalSection">Document Prefix</label>
                                            <input type="text" name="document_prefix" class="form-control" id="alphabeticalSection" aria-describedby="alphabericalSection" class="dark-text" placeholder="Enter alphabetical section" value="{{ $singleDocumentLabel->document_prefix }}">
                                          </div>
                                        </div>

                                        <div class="col">
                                          <div class="form-group">
                                            <label for="numericalSection">Document Suffix</label>
                                            <input type="number" step="1" name="document_suffix" class="form-control" id="numericalSection" aria-describedby="numericalSection" class="dark-text" placeholder="Enter numerical section" value="{{ $singleDocumentLabel->document_suffix }}">
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

@include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
   
  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')
