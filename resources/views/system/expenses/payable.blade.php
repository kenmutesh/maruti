@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | AP Aging Report',
'datatable' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />

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
      <div class="d-flex justify-content-between px-4">
        <button type="button" name="button" onclick="printDirectInfo('AP Report.pdf')" class="btn btn-info col-sm-3">
            PRINT DIRECTLY
        </button>
          <div class="d-flex col-5 mb-2 justify-content-between">
    
            <div class="col-12 d-flex p-0 px-1 justify-content-around align-items-center">
                <p class="m-0">Date</p>
                <input type="date" class="rounded-sm border border-dark p-1 col ml-1" value="{{ date('Y-m-d', $time) }}" onchange="window.location.href='?date=' + this.value">
            </div>
  
  
          </div>
      </div>
      <div class="card p-4 report">
        
        <div class="card-header text-center">
          <p class="card-title p-0 m-0 font-weight-bold">A\P Aging Report As of: {{ date('d/m/Y', $time) }}</p4>
        </div>
        <div class="card-body p-0">
                    <div class="table-responsive p-0">
                      <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table">
              <thead class=" text-primary">
                <tr>
                  <th class="p-0">
                    Customer Name
                  </th>
                  <th class="p-0">
                    Current
                  </th>
                  <th class="p-0">
                    1 - 30
                  </th>
                  <th class="p-0">
                    31 - 60
                  </th>
                  <th class="p-0">
                    61 - 90
                  </th>
                  <th class="p-0">
                    Total
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $currentTotalGrand = 0;
                $thirtyDayTotalGrand = 0;
                $sixtyDayTotalGrand = 0;
                $ninetyDayTotalGrand = 0;
                $excessDayTotalGrand = 0;
                $grandTotalGrand = 0;
                  foreach ($supplierTransactions as $transaction) {
                ?>
                  <tr>
                    <td class="p-0">
                      <?php echo $transaction->supplier_name ?>
                    </td>
                    <?php

                      $secondsPerDay = 86400;

                      $thirtyDay = $secondsPerDay * 30;

                      $sixtyDay = $secondsPerDay * 60;

                      $ninetyDay = $secondsPerDay * 90;

                      $todayStart = $time;

                      $currentTotal = 0; // all unpaid
                      $thirtyDayTotal = 0; // today upto past 30 days; 
                      $sixtyDayTotal = 0; // today upto past 60 days;
                      $ninetyDayTotal = 0; // today upto past 30 days;
                      $grandTotal = 0; // total paid or unpaid;

                      foreach ($transaction->purchaseorders as $purchaseorder) {
                        // if not exceeding 86,400 seconds it is from today
                        $orderDate = strtotime($purchaseorder->date_created);
                        if (($orderDate - $todayStart) <= $thirtyDay) {
                          if($purchaseorder->amount_due > 0){
                            $currentTotal = floatval($purchaseorder->amount_due) + floatval($currentTotal);
                          }
                          $thirtyDayTotal = floatval($purchaseorder->amount_due) + floatval($thirtyDayTotal);
                        }else if(($orderDate - $todayStart) <= $sixtyDay){
                          if($purchaseorder->amount_due > 0){
                            $currentTotal = floatval($purchaseorder->amount_due) + floatval($currentTotal);
                          }
                          $sixtyDayTotal = floatval($purchaseorder->amount_due) + floatval($sixtyDayTotal);
                        }else if(($orderDate - $todayStart) <= $ninetyDay){
                          if($purchaseorder->amount_due > 0){
                            $currentTotal = floatval($purchaseorder->amount_due) + floatval($currentTotal);
                          }
                          $ninetyDayTotal = floatval($purchaseorder->amount_due) + floatval($ninetyDayTotal);
                        }
                        if($orderDate <= $todayStart){
                          $grandTotal = floatval($purchaseorder->amount_due) + floatval($grandTotal);
                        }
                      }

                      foreach ($transaction->creditnotes as $creditnote) {
                        // if not exceeding 86,400 seconds it is from today
                        $orderDate = strtotime($creditnote->date_created);
                        if (($orderDate - $todayStart) <= $thirtyDay) {
                          if($creditnote->grand_total > 0){
                            $currentTotal = floatval($currentTotal) - floatval($creditnote->grand_total);
                          }
                          $thirtyDayTotal = floatval($thirtyDayTotal) - floatval($creditnote->grand_total);
                        }else if(($orderDate - $todayStart) <= $sixtyDay){
                          if($creditnote->grand_total > 0){
                            $currentTotal = floatval($currentTotal) - floatval($creditnote->grand_total);
                          }
                          $sixtyDayTotal = floatval($sixtyDayTotal) - floatval($creditnote->grand_total);
                        }else if(($orderDate - $todayStart) <= $ninetyDay){
                          if($creditnote->grand_total > 0){
                            $currentTotal = floatval($currentTotal) - floatval($creditnote->grand_total);
                          }
                          $ninetyDayTotal = floatval($ninetyDayTotal) - floatval($creditnote->grand_total);
                        }
                        if($orderDate <= $todayStart){
                          $grandTotal = floatval($grandTotal) - floatval($creditnote->grand_total);
                        }
                      }

                      $currentTotalGrand = $currentTotal + floatval($currentTotalGrand);
                      $thirtyDayTotalGrand = $thirtyDayTotal + floatval($thirtyDayTotalGrand);
                      $sixtyDayTotalGrand = $sixtyDayTotal + floatval($sixtyDayTotalGrand);
                      $ninetyDayTotalGrand = $ninetyDayTotal + floatval($ninetyDayTotalGrand);
                      $grandTotalGrand = $grandTotal + floatval($grandTotalGrand);
                    
                    ?>
                    <td class="p-0">
                      <?php
                        echo number_format($currentTotal, 2);
                      ?>
                    </td>
                    <td class="p-0">
                    <?php
                        echo number_format($thirtyDayTotal, 2);
                      ?>
                    </td>
                    <td class="p-0">
                    <?php
                        echo number_format($sixtyDayTotal, 2);
                      ?>
                    </td>
                    <td class="p-0">
                    <?php
                        echo number_format($ninetyDayTotal, 2);
                      ?>
                    </td>
                    <td class="p-0">
                      <?php
                        echo number_format($grandTotal, 2);
                      ?>
                    </td>
                  </tr>
                <?php
                  }

                  ?>

              </tbody>
                <tr>
                  <td class="p-0">
                    Total
                  </td>
                  <td class="p-0">
                    <?php echo number_format($currentTotalGrand,2); ?>
                  </td>
                  <td class="p-0">
                    <?php echo number_format($thirtyDayTotalGrand,2); ?>
                  </td>
                  <td class="p-0">
                    <?php echo number_format($sixtyDayTotalGrand,2); ?>
                  </td>
                  <td class="p-0">
                    <?php echo number_format($ninetyDayTotalGrand,2); ?>
                  </td>
                  <td class="p-0">
                    <?php echo number_format($grandTotalGrand,2); ?>
                  </td>
                </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
</div>
<script src="/assets/js/plugins/html2pdf.bundle.min.js"></script>
<script>
  function printDirectInfo() {
      html2pdf().from(document.querySelector('.report')).toPdf().get('pdf').then(function(pdfObj) {
        // pdfObj has your jsPDF object in it, use it as you please!
        pdfObj.autoPrint();
        window.open(pdfObj.output('bloburl'), '_blank');
      });
    }
</script>
@include('universal-layout.scripts',
  [
  'jquery' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'datatable' => true,
  ]
  );

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
          (min <= date && date <= max)) {
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
          dom: 'Bfrtip',
          buttons: [
            'pdf', 'print'
          ]
        });

        dataTableInstances.push(table);
        const printers = document.querySelectorAll('.dt-buttons button');
        printers.forEach((printerBtn) => {
          printerBtn.classList.add('btn', 'btn-warning');
        })
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

    function resetOptions(input) {
      dataTableInstances[0].search(input.value).draw();
    }

    function sortTable(selectElement) {
      dataTableInstances[0].order([selectElement.selectedIndex-1, 'asc']).draw()
    }
  </script>
  @include('universal-layout.footer')