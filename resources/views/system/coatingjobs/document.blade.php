@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Document',
'bootstrapselect' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style media="screen">
  @font-face {
    font-family: 'Verdana';
    src: url('/assets/fonts/verdana.ttf');
  }

  * {
    font-family: 'Verdana';
  }
</style>

<body class="bg-secondary">
  @include('universal-layout.spinner')

  <div class="row d-flex justify-content-around w-75 mx-auto">
    <button type="button" name="button" onclick="printInfo('JobCard :{{ $coatingjob->created_at }}.pdf')" class="btn btn-warning col-sm-3">
      SAVE TO LOCAL
    </button>

    <button type="button" name="button" onclick="printDirectInfo('JobCard :{{ $coatingjob->created_at }}.pdf.pdf')" class="btn btn-warning col-sm-3">
      PRINT DIRECTLY
    </button>

    @if($hidePrice)
    <a href="/coatingjobs/{{ $coatingjob->id }}" type="button" name="button" class="btn btn-warning col-sm-2">
      VIEW AS QUOTATION
    </a>
    @else
    <a href="/coatingjobs/{{ $coatingjob->id }}?hideprice=true" type="button" name="button" class="btn btn-warning col-sm-2 <?php echo $disabledState = ($coatingjob->coating_suffix == '' || $coatingjob->coating_suffix == null) ? 'disabled text-white' : ''; ?>">
      VIEW AS JOB CARD
    </a>
    @endif

    <div class="dropdown">
      <button type="button" class="dropdown-toggle d-flex align-items-center btn btn-warning" data-toggle="dropdown">
        SEND DOCUMENT AS EMAIL
      </button>
      <div class="dropdown-menu text-center p-4">
        <div class="form-group">
          <label for="">Email</label>
          <input type="email" name="email" required placeholder="Please insert email">
        </div>
        <button type="button" name="button" class="btn btn-success" onclick="sendPDF(this, 'JobCard :{{ $coatingjob->created_at }}.pdf')">
          SEND DOCUMENT
        </button>
      </div>
    </div>
  </div>

  <div class="col-sm-12 mt-3 mx-auto h-100" style="width:100%;background:white;min-height:29cm;" id="documentFrame" style="background:white;font-family:'Helvetica';font-weight: lighter;">
    @if($coatingjob->status == App\Enums\CoatingJobStatusEnum::CANCELLED)
    <div class="position-absolute h-100 w-100 d-flex justify-content-center align-items-center" style="background-color: #ff36364d !important;">
      <h1 class="display-1" style="transform: rotate(45deg);">Cancelled</h1>
    </div>
    @endif
    @if($hidePrice)
    <h5 class="text-center">JOB CARD</h5>
    @else
    <h5 class="text-center">QUOTATION</h5>
    @endif

    <div class="row">
      <div class="col-sm-11 d-flex justify-content-between">
        <div class="col-sm-6 d-flex flex-column ml-2">
          <img src="/assets/img/maruti-square.png" width="75px" alt="">
          <span style="width:auto;font-size:8px;">
            <span style="width:auto;font-size:8px;color:#ff6000;">Maruti</span>
            <span style="width:auto;font-size:8px;color:#00ff00;">Glazers</span>
            <span style="width:auto;font-size:8px;color:#ff6000;">Limited</span>
          </span>
          <span style="color:#000;width:auto;font-size:8px;">PO Box 14-00623, Nairobi - Kenya.</span>
          <span style="color:#000;width:auto;font-size:8px;">Tel: 0714 452 313, 0737196628</span>
          <span style="color:#000;width:auto;font-size:8px;">info@maruti.co.ke</span>
          @if(!$hidePrice)
          <span style="color:#000;width:auto;font-size:8px;">P051354459I</span>
          <span style="color:#000;width:auto;font-size:8px;">Mombasa Road, Kaysalt Complex, Godown B14</span>
          @endif
          <span class="border border-top-0 border-left-0 border-right-0 border-lg border-dark w-25"></span>
        </div>

        <div class="col-sm-5 d-flex flex-column align-items-end" style="color:#000;margin-right: -8%;">
          <div class="d-flex flex-column p-2">
            <span style="width:auto;font-size:10px">
              @if($hidePrice)
              Job Card: <b>{{ $coatingjob->coating_prefix }}{{ $coatingjob->coating_suffix }}</b>
              @else
              Quotation: <b>{{ $coatingjob->quotation_prefix }}{{ $coatingjob->quotation_suffix }}</b>
              @endif
            </span>
            <span style="width:auto;font-size:10px">
              Date Created: {{ date('d-M-Y',strtotime( $coatingjob->created_at )) }}
            </span>
            <span style="width:auto;font-size:10px">
              Customer: <b>{{ $coatingjob->customer->customer_name }}</b>
              @if($coatingjob->cash_sale_name)
              <b>
                {{ $coatingjob->cash_sale_name }}
              </b>
              @endif
            </span>
          </div>
          <div class="d-flex flex-column p-2" style="border:1px solid #ff6600;width: max-content;">
            <span style="color:#000;width:auto;font-size:8px;">PRODUCT: {{ $coatingjob->belongs_to->humanreadablestring() }}</span>
            <span style="color:#000;width:auto;font-size:8px;">LPO: {{ $coatingjob->lpo ?? '-' }}</span>
            <span style="color:#000;width:auto;font-size:8px;">RAL: {{ $coatingjob->powder->powder_color ?? '-' }}</span>
            <span style="color:#000;width:auto;font-size:8px;">{{ $coatingjob->powder->supplier->supplier_name ?? '-' }}</span>
            <span style="color:#000;width:auto;font-size:8px;">KG: {{ $coatingjob->goods_weight ?? '-' }}</span>
            @if($coatingjob->profile_type->humanreadablestring() != 'N/A')
            <span style="color:#000;width:auto;font-size:8px;">PF TYPE: {{ $coatingjob->profile_type->humanreadablestring() ?? '-' }}</span>
            @endif
            <span style="color:#000;width:auto;font-size:8px;">PWD EST: <span id="powderEstimate">{{ $coatingjob->powder_estimate ?? '-' }}</span> KG</span>
            @if($coatingjob->in_date != null)
            <span style="color:#000;width:auto;font-size:8px;">IN: {{ date('d-m-Y',strtotime( $coatingjob->in_date )) ?? '-' }}</span>
            @endif
          </div>
        </div>

      </div>
    </div>

    <div class="row mt-4 p-1">
      <table class="col-sm-11 mx-auto" style="border-collapse:collapse;font-size:8px;">
        <thead style="color:#000;" class="text-center">
          <th style="border:1px solid #ff6600;">Code</th>
          <th style="border:1px solid #ff6600;">Color/Name</th>
          <th style="border:1px solid #ff6600;">Quantity</th>
          <th style="border:1px solid #ff6600;">UoM</th>
          <th style="border:1px solid #ff6600;">KG</th>
          @if(!$hidePrice)
          <th style="border:1px solid #ff6600;">@</th>
          <th style="border:1px solid #ff6600;">Amount</th>
          @endif
        </thead>
        <tbody>
          @foreach($coatingjob->marutiitems as $marutiitem)
          @if($marutiitem->inventory_item_id)
          <tr style="color:#000;">
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->inventoryitem->item_code }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->inventoryitem->item_name }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            @if(!$hidePrice)
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
            @endif
          </tr>
          @endif

          @if($marutiitem->powder_id)
          <tr style="color:#000;">
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->powder->powder_code }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->powder->powder_color }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
            @if(!$hidePrice)
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
            @endif
          </tr>
          @endif

          @endforeach

          @foreach($coatingjob->aluminiumitems as $aluminiumitem)
          <tr style="color:#000;">
            <td style="border:1px solid #ff6600;padding:5px;">N/A</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->item_name }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->quantity }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->uom }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->item_kg }}</td>
            @if(!$hidePrice)
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format(($aluminiumitem->unit_price_without_vat), 2) }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->sub_total, 2) }}</td>
            @endif
          </tr>
          @endforeach

          @foreach($coatingjob->steelitems as $steelitem)
          <tr style="color:#000;">
            <td style="border:1px solid #ff6600;padding:5px;">N/A</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->item_name }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->quantity }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->uom }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            @if(!$hidePrice)
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->unit_price_without_vat, 2) }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->sub_total, 2) }}</td>
            @endif
          </tr>
          @endforeach
        </tbody>
        @if(!$hidePrice)
        <tfoot>
          <tr>
            <td style="border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="4"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">SUB TOTAL</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($coatingjob->sub_total, 2) }}</td>
          </tr>
          <tr>
            <td style="border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="4"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">VAT</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($coatingjob->vat_addition, 2) }}</td>
          </tr>
          <tr>
            <td style="border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="4"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">GRAND TOTAL</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($coatingjob->grand_total, 2) }}</td>

          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    <div class="row mt-4 p-1" style="color:#000;font-size:8px;">
      <div class="col-sm-11">
        <div class="col-sm-7 d-flex flex-column ml-3">
          <div class="border border-top-0 border-left-0 border-right-0 border-lg border-dark w-50 mt-4">
            <span>Approved By: {{ $coatingjob->preparedBy->username }}</span>
          </div>
        </div>
      </div>
    </div>
    @if($hidePrice)
    <div class="row p-1">
      <div class="col-sm-11 mx-auto p-0 font-10">
        <table class="w-100">
          <tbody>
            <tr style="color:#000;">
              <td style="border:1px solid #000;padding:5px;" colspan="100%">
                <div class="row p-0 m-0 justify-content-between">
                  <div class="col-2 p-0 m-0" style="font-size:8px;">
                    GOODS RCVD BY:
                  </div>
                  <div class="col-1 p-0 m-0" style="font-size:8px;">
                    DATE:
                  </div>
                  <div class="col-2 p-0 m-0" style="font-size:8px;">
                    SUPERVISOR:
                  </div>
                  <div class="col-1 p-0 m-0" style="font-size:8px;">
                    DATE:
                  </div>
                  <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
                    S/CARD POSTED:
                    <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span> YES
                    <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span> NO
                  </div>
                </div>
              </td>
            </tr>
            <tr style="color:#000;">
              <td style="border:1px solid #000;padding:5px;" colspan="100%">
                <div class="row p-0 m-0 justify-content-between">
                  <div class="col-6 row">
                    <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
                      AUTO
                      <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span>
                    </div>
                    <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
                      AUTOMANUAL
                      <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span>
                    </div>
                    <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
                      BOOTH 1
                      <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span>
                    </div>
                    <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
                      BOOTH 2
                      <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span>
                    </div>
                  </div>
                  <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
                    PC BY:
                  </div>
                  <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
                    DATE:
                  </div>
                </div>
              </td>
            </tr>
            <tr style="color:#000;">
              <td style="border:1px solid #000;padding:5px;" colspan="100%">
                <div class="align-items-center col-4 d-flex m-0 p-0" style="font-size:8px;">
                  SUPERVISOR COMMENT:
                </div>
      </div>
      </td>
      </tr>
      <tr style="color:#000;">
        <td style="border:1px solid #000;padding:5px;" colspan="100%">
          <div class="row p-0 m-0 justify-content-between">
            <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
              PACKED BY:
            </div>
            <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
              DATE:
            </div>
            <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
              TTL PCS:
            </div>
            <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
              TTL BUNDLE:
            </div>
            <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
              QUALITY:
              <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span> APPROVED
              <span class="text-white border border-dark d-inline-block mx-2" style="width: 10px;height: 10px;">A</span> REJECTED
            </div>
          </div>
        </td>
      </tr>
      <tr style="color:#000;">
        <td style="border:1px solid #000;padding:5px;" colspan="100%">
          <div class="align-items-center d-flex m-0 p-0" style="font-size:8px;">
            PACKAGING COMMENT:
          </div>
    </div>
    </td>
    </tr>
    <tr style="color:#000;">
      <td style="border:1px solid #000;padding:5px;" colspan="100%">
        <div class="row p-0 m-0 justify-content-between">
          <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
            DISPATCHED BY:
          </div>
          <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
            DATE:
          </div>
          <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
            INV NO:
          </div>
          <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
            C/S NO:
          </div>
        </div>
      </td>
    </tr>
    <tr style="color:#000;">
      <td style="border:1px solid #000;padding:5px;" colspan="100%">
        <div class="align-items-center col-2 d-flex m-0 p-0" style="font-size:8px;">
          DISPATCH COMMENT:
        </div>
  </div>
  </td>
  </tr>
  </tbody>
  </table>
  </div>
  </div>
  @endif

  <div style="position: absolute;bottom: 0cm;left: 0cm;right: 0cm;height: auto;">
    @if(!$hidePrice)
    <div class="row p-1">
      <div class="col-sm-11 mx-auto p-0 font-10">
        <table class="w-100">
          <tbody>
            <tr style="color:#000;">
              <td style="border:1px solid #000;padding:5px;" colspan="100%">
                <div class="row p-0 m-0 justify-content-between">
                  <div class="col p-0 m-0" style="font-size:8px;">
                    BANK DETAILS:
                  </div>
                </div>
              </td>
            </tr>
            <tr style="color:#000;">
              <td style="border:1px solid #000;padding:5px;" colspan="100%">
                <div class="row p-0 m-0 justify-content-between">
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    I&M BANK
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    A/C: 010 005 655 612 10
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    PARKLANDS BRANCH
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    BANK CODE: 057
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    BRANCH CODE: 010
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    SWIFT CODE: IMBLKENA
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    MPESA PAYBILL: 542542
                  </div>
                  <div class="col-12 p-0 m-0" style="font-size:8px;">
                    A/C NAME: 44448
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    @endif
    <div class="text-center d-flex justify-content-center align-content-center" style="line-height: 35px;">
      <p>This is a document generated by the <img src="/assets/img/aprotec-ico.png" alt="Aprotec" width="30px"> system</p>
    </div>
  </div>
  </div>

  <script src="/assets/js/plugins/html2pdf.bundle.min.js"></script>

  <script type="text/javascript">
    async function printInfo(filename) {
      const worker = html2pdf();

      worker.set({
        pagebreak: {
          mode: ['avoid-all', 'css', 'legacy']
        },
        html2canvas: {
          scale: 8
        },
      });

      await worker.from(document.querySelector('#documentFrame'));

      let myPdf = await worker.save(filename);
    }

    async function printDirectInfo() {
      const worker = html2pdf();
      worker.set({
        pagebreak: {
          mode: ['avoid-all', 'css', 'legacy']
        },
        html2canvas: {
          scale: 8
        },
      });
      worker.from(document.querySelector('#documentFrame')).toPdf().get('pdf').then(function(pdfObj) {
        // pdfObj has your jsPDF object in it, use it as you please!
        pdfObj.autoPrint();
        window.open(pdfObj.output('bloburl'), '_blank');
      });
    }


    async function sendPDF(btnElement, documentName) {
      const email = document.querySelector('input[name="email"]');

      if (email.value == '') {
        alert("Email needs to be filled");
        return false;
      }

      if (!email.checkValidity()) {
        alert("Email entered failed validity check");
        return false;
      }
      // reference to the previous html content
      const previousHTML = btnElement.parentElement.previousElementSibling.innerHTML;
      btnElement.parentElement.previousElementSibling.disabled = true;
      // show the spinner
      btnElement.parentElement.previousElementSibling.innerHTML = '<span class="spinner-border text-dark"></span><span>SENDING</span>';

      const worker = html2pdf();
      await worker.from(document.querySelector('#documentFrame'));

      worker.set({
        pagebreak: {
          mode: ['avoid-all', 'css', 'legacy']
        },
        html2canvas: {
          scale: 8
        },
      });

      const myPdf = await worker.outputPdf('datauristring', documentName);
      const preBlob = dataURItoBlob(myPdf);
      const file = new File([preBlob], documentName, {
        type: 'application/pdf'
      });

      const message = "<p>Please find the Coating Job attached: " + documentName.split('.')[0] + " </p>";

      const subject = "Coating Job " + documentName;

      const result = await sendFile(file, email.value, message, subject);

      if (result == 'error' || !result.status) {
        console.log(result);
        alert('Failed to send');
      } else {
        console.log(result);
        alert('Sent successfully');
      }
      btnElement.parentElement.previousElementSibling.innerHTML = previousHTML;
      btnElement.parentElement.previousElementSibling.disabled = false;
    }

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

    async function sendFile(userFile, email, message, subject) {
      let filePaths;
      try {
        let formData = new FormData();
        formData.append("file", userFile);
        formData.append("email", email);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("message", message);
        formData.append("subject", subject);
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
  </script>

  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  ]
  )
  @include('universal-layout.footer')