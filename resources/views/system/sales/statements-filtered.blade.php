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

  #leftsidebar {
    z-index: 4;
  }
</style>
<?php
$appendString = '';
if (isset($from) && isset($to)) {
  $appendString = '?date=' . $statementDate . '&from=' . $from . '&to=' . $to;
} else if (isset($from)) {
  $appendString = '?date=' . $statementDate . '&from=' . $from;
} else if (isset($to)) {
  $appendString = '?date=' . $statementDate . '&to=' . $to;
}
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
                    <h4 class="card-title p-0 m-0">Customer Statements</h4>
                  </div>
                  <div class="card-body p-0">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#filterStatements">
                      Filters Statements
                    </button>

                    <button class="btn btn-success" onclick="sendStatements()">
                      Open Statements For Sending
                    </button>

                    <!-- The Modal -->
                    <div class="modal fade" id="filterStatements">
                      <div class="modal-dialog modal-dialog-centered" style="z-index: 5;">
                        <div class="modal-content">
                          <form action="/sales/statements-filtered" method="GET">
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
                                <label for="">Choose customers</label>
                                <select name="customers[]" class="form-control" id="" multiple data-live-search="true" data-style="text-white">
                                  <option value="all">All Customers</option>
                                  @foreach($allCustomers as $singleCustomer)
                                  <option value="{{ $singleCustomer->id }}">
                                    {{ $singleCustomer->company }}
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
                            Customer Name and Email
                          </th>
                          <th class="p-0">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($customers as $singleCustomer)
                        <tr>
                          <td class="p-0" data-customer-id="{{ $singleCustomer->id }}" data-customer-email="{{ $singleCustomer->contact_person_email }}">
                            {{ $singleCustomer->company }} - {{ $singleCustomer->contact_person_email }}
                          </td>
                          <td class="p-0">
                            <a href="/sales/statements/viewdoc/{{ $singleCustomer->id }}{{ $appendString }}" target="_blank" data-close class="btn btn-info mb-2 btn-sm print-statement-btn">
                              PRINT
                            </a>
                            <button class="btn btn-sm btn-info preview-statement-btn d-none" type="button" name="button" data-toggle="modal" data-target="#statement{{ $singleCustomer->id }}">
                              PREVIEW STATEMENT
                            </button>
                            <div class="modal fade" style="z-index: 5;" id="statement{{ $singleCustomer->id }}" tabindex="-1" role="dialog">
                              <div class="modal-dialog modal-xl">
                                <div class="modal-content">

                                  <!-- Modal Header -->
                                  <div class="modal-header">
                                    <h4>Statement Preview</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  </div>

                                  <!-- Modal body -->
                                    <?php
                                    $message = "Dear " . $singleCustomer->customer_name . ",<br/>Your statement is attached. Please remit payment at your earliest convenience.Thank you for your business - we appreciate it very much.<br/>Sincerely,<br/>MARUTI GLAZERS LTD"
                                    ?>
                                  <div class="modal-body" data-view="/sales/statements/viewdoc-bare/{{ $singleCustomer->id }}{{ $appendString }}" data-message="{{ $message }}" data-cc-email="{{ $singleCustomer->cc_emails }}"
                                  data-email="{{ $singleCustomer->contact_person_email }}">
                                    <div class="text-center init-statement-loader">
                                      <span class="spinner-border"></span>
                                    </div>
                                  </div>

                                  <!-- Modal footer -->
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                  </div>

                                </div>
                              </div>
                            </div>
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
  ])
  <script src="/assets/js/plugins/html2pdf.bundle.min.js"></script>
  <script>
    async function sendStatements() {
      const previewBtn = document.querySelectorAll('.preview-statement-btn');
      for (const preview of previewBtn) {
        preview.click();
        const statementLoaderParent = preview.parentElement.querySelector('.modal-body');
        const dataViewURL = statementLoaderParent.getAttribute('data-view');
        const message = statementLoaderParent.getAttribute('data-message');
        const ccEmails = JSON.parse(statementLoaderParent.getAttribute('data-cc-email'));
        const mainEmail = JSON.parse(statementLoaderParent.getAttribute('data-email'));
        const request = await fetch(dataViewURL);
        const html = await request.text();
        statementLoaderParent.innerHTML = html;
        const worker = html2pdf();
          await worker.from(statementLoaderParent);

          worker.set({
            pagebreak: {
              mode: ['avoid-all', 'css', 'legacy']
            },
            html2canvas: {
              scale: 4
            },
          });
          const documentName = 'statement.pdf';
          const myPdf = await worker.outputPdf('datauristring', documentName);
          const preBlob = dataURItoBlob(myPdf);

          const file = new File([preBlob], documentName, {
            type: 'application/pdf'
          });

          const subject = "STATEMENT FROM MARUTI GLAZERS";
          // const result = await sendFile(file, mainEmail, message, subject, ccEmails, ['info@maruti.co.ke']);
      }
    }

    async function sendFile(userFile, email, message, subject, carbonCopies = [], blindCarbonCopies = []) {
      let filePaths;
      try {
        let formData = new FormData();
        formData.append("file", userFile);
        formData.append("email", email);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("message", message);
        formData.append("subject", subject);
        formData.append("carbon_copy", carbonCopies);
        formData.append("blind_carbon_copy", blindCarbonCopies);
        let response = await fetch('/attachment-email', {
          method: 'POST',
          body: formData
        });

        filePaths = await response.json();

        return filePaths;
      } catch (e) {
        console.log(e);
        return 'error';
      }

    }

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
    function dataURItoBlob(dataURI) {
      // convert base64/URLEncoded data component to raw binary data held in a string
      let byteString;
      if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
      else
        byteString = unescape(dataURI.split(',')[1]);

      // separate out the mime component
      let mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

      // write the bytes of the string to a typed array
      let ia = new Uint8Array(byteString.length);
      for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }

      return new Blob([ia], {
        type: mimeString
      });
    }
  </script>
  @include('universal-layout.footer')