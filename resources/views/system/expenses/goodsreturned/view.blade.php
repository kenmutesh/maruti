
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Goods Returned Notes',
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
  'slug' => '/expenses'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

      <div class="content">

        <div class="row">
          <a href="/expenses/goodsreturned-note" type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 w-25">
            <i class="tim-icons icon-simple-add"></i> CREATE GOODS RETURNED NOTE
          </a>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">Goods Returned Notes</h4>
            </div>
            <div class="card-body p-0">
            <div class="py-3 row m-0">
                    <div class="d-flex flex-column col-sm-6 invisible">
                      Pages: 
                    </div>
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
                        Supplier
                      </th>
                      <th class="p-0 border">
                        Invoice Details
                      </th>
                      <th class="p-0 border">
                        Credit Note Details
                      </th>
                      <th class="p-0 border">
                        Grand Total
                      </th>
                      <th class="p-0 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($goodsReturnedNotes as $singleGoodsReturnedNote)
                      <tr>
                        <td class="p-0">
                          {{ $singleGoodsReturnedNote->date_created }}
                        </td>

                        <td class="p-0">
                          {{ $singleGoodsReturnedNote->supplier->supplier_name }}
                        </td>

                        <td class="p-0">
                          <?php echo $singleGoodsReturnedNote->invoice_ref ?>
                          <div class="dropdown">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                              Attached Documents
                            </button>
                            <div class="dropdown-menu">
                              <?php
                                if ($singleGoodsReturnedNote->invoice_docs == '-' || $singleGoodsReturnedNote->invoice_docs == NULL) {
                              ?>
                                  <a class="dropdown-item" href="">No Attachments</a>
                              <?php
                              }else {
                                $invoiceDocs = json_decode($singleGoodsReturnedNote->invoice_docs);

                                foreach ($invoiceDocs as $invoiceDoc) {
                              ?>
                                  <a class="dropdown-item" target="_blank" href="/document_uploads/<?php echo $invoiceDoc->system_name ?>">
                                    <?php echo $invoiceDoc->original_name ?>
                                  </a>
                              <?php
                              }
                            }
                              ?>
                            </div>
                          </div>
                        </td>

                        <td class="p-0">
                          <?php echo $singleGoodsReturnedNote->credit_note_ref ?>
                          <div class="dropdown">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                              Attached Documents
                            </button>
                            <div class="dropdown-menu">
                              <?php
                                if ($singleGoodsReturnedNote->credit_note_docs == '-' || $singleGoodsReturnedNote->credit_note_docs == NULL) {
                              ?>
                                  <a class="dropdown-item" href="">No Attachments</a>
                              <?php
                              }else {
                                $creditNoteDocs = json_decode($singleGoodsReturnedNote->credit_note_docs);

                                foreach ($creditNoteDocs as $creditNoteDoc) {
                              ?>
                                  <a class="dropdown-item" target="_blank" href="/document_uploads/<?php echo $creditNoteDoc->system_name ?>">
                                    <?php echo $creditNoteDoc->original_name ?>
                                  </a>
                              <?php
                              }
                            }
                              ?>
                            </div>
                          </div>
                        </td>

                        <td class="p-0">
                          {{ number_format($singleGoodsReturnedNote->grand_total, 2) }}
                        </td>

                        <td class="p-0">
                          <a href="/expenses/goodsreturned-note/viewdoc/{{ $singleGoodsReturnedNote->id }}" target="_blank" type="button" name="button" class="btn btn-info btn-sm" >PRINT</a>

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
