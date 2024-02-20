@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Statements',
'datatable' => true,
'bootstrapselect' => true,
]
)
<style>
  .modal-backdrop.show {
    z-index: -1;
  }
</style>
<?php
$appendString = '';
if (isset($from) && isset($to)) {
  $appendString = '?statement_date=' . $statementDate . '&from=' . $from . '&to=' . $to;
} else if (isset($from)) {
  $appendString = '?statement_date=' . $statementDate . '&from=' . $from;
} else if (isset($to)) {
  $appendString = '?statement_date=' . $statementDate . '&to=' . $to;
}

$allowedSuppliers = $selectedSuppliers;
?>

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
        <div class="main-panel">

          <div class="content">
            <div class="row">

              <div class="col-md-12">
                <div class="card ">
                  <div class="card-header">
                    <h4 class="card-title p-0 m-0">Supplier Statements</h4>
                  </div>
                  <div class="card-body p-0">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#filterStatements">
                      Filters Statements
                    </button>

                    <!-- The Modal -->
                    <div class="modal fade" id="filterStatements">
                      <div class="modal-dialog modal-dialog-centered" style="z-index: 5;">
                        <div class="modal-content">
                          <form action="/suppliers/statements" method="GET">
                            <!-- Modal body -->
                            <div class="modal-body">
                              <div class="row flex-column">
                                <div class="form-group col-12">
                                  <p class="m-0">Statement Period From:</p>
                                  <input type="date" class="col form-control border-dark rounded-sm" name="from">
                                </div>
                                <div class="form-group col-12">
                                  <p class="m-0">Statement Period To:</p>
                                  <input type="date" class="col form-control border-dark rounded-sm" name="to">
                                </div>
                                <div class="form-group col-12">
                                  <p class="m-0">Statement Date</p>
                                  <input type="date" required class="col form-control border-dark rounded-sm" name="statement_date">
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="">Choose suppliers</label>
                                <select name="suppliers[]" class="form-control" id="" multiple data-live-search="true" data-style="text-white">
                                  <option value="all">All Suppliers</option>
                                  @foreach($suppliers as $supplier)
                                  <option value="{{ $supplier->id }}">
                                    {{ $supplier->supplier_name }}
                                  </option>
                                  @endforeach
                                </select>
                              </div>
                            </div>


                            <!-- Modal footer -->
                            <div class="modal-footer">
                              <button class="btn btn-success col-12 mx-auto">
                                View Statements
                              </button>
                          </form>
                        </div>

                      </div>
                    </div>
                  </div>
                  <div class="table-responsive p-0">
                    <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table">
                      <thead class=" text-primary">
                        <tr>
                          <th class="p-0">
                            Supplier Name
                          </th>
                          <th class="p-0">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($suppliers as $supplier)
                        @if($selectedSuppliers)
                          @if($selectedSuppliers[0] != 'all')
                            @if(!in_array($supplier->id, $selectedSuppliers))
                              @continue
                            @endif
                          @endif
                        @endif
                        <tr>
                          <td class="p-0">
                            {{ $supplier->supplier_name }}
                          </td>
                          <td class="p-0">
                            @if($appendString == "")
                            <span class="text-muted">Select days from filter first</span>
                            @else
                            <a href="/suppliers/statements/{{ $supplier->id }}{{ $appendString }}" target="_blank" data-close class="btn btn-info mb-2 btn-sm print-statement-btn">
                              PRINT
                            </a>
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
  @include('universal-layout.alert')
  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'datatable' => true,
  'bootstrapselect' => true,
  ]
  )
  <script>
    const printStatementBtn = document.querySelectorAll(".print-statement-btn");
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip(); //for tooltip functionality

      $('.data-table').each((index, element) => {
        const table = $(element).DataTable({
          paging: false,
          aaSorting: [],
        });

      })

    });
  </script>
  @include('universal-layout.footer')