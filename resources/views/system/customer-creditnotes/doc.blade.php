@include('universal-layout.header', ['pageTitle' => 'Aprotec | Purchase Order Document'])
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

  <div class="row d-flex justify-content-around w-100 mx-auto">
    <button type="button" name="button" onclick="printInfo('CreditNote:{{ $creditNote->lpo_prefix }}-{{ $creditNote->lpo_suffix }}.pdf')" class="btn btn-info col-sm-3">
      SAVE TO LOCAL
    </button>

    <button type="button" name="button" onclick="printDirectInfo('CreditNote:{{ $creditNote->lpo_prefix }}-{{ $creditNote->lpo_suffix }}.pdf')" class="btn btn-info col-sm-3">
      PRINT DIRECTLY
    </button>


    <div class="dropdown">
      <button type="button" class="btn btn-info dropdown-toggle d-flex align-items-center" class="btn btn-info col-sm-3" data-toggle="dropdown">
        SEND DOCUMENT AS EMAIL
      </button>
      <div class="dropdown-menu p-4">
        <div class="form-group">
          <label for="">Email</label>
          <input type="email" name="email" required placeholder="Please insert email">
        </div>
        <button type="button" name="button" class="btn btn-info" onclick="sendPDF(this, 'CreditNote:{{ $creditNote->lpo_prefix }}-{{ $creditNote->lpo_suffix }}.pdf')">
          SEND DOCUMENT
        </button>
      </div>
    </div>
  </div>
  <div class="col-sm-12 mt-3 mx-auto" style="width:100%;background:white;min-height:29cm;" id="documentFrame" style="background:white;font-family:'Helvetica';font-weight: lighter;">
    @if($creditNote->cancelled_at)
    <div class="position-absolute h-100 w-100 d-flex justify-content-center align-items-center" style="background-color: #ff36364d !important;">
      <h1 class="display-1" style="transform: rotate(45deg);">Cancelled</h1>
    </div>
    @endif
    <h5 class="text-center">CREDIT NOTE</h5>

    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <div class="col-sm-6 d-flex flex-column ml-2">
          <img src="/assets/img/maruti-square.png" width="75px" alt="">
          <span style="width:auto;font-size:12px;">
            <span style="width:auto;font-size:12px;color:#ff6000;">Maruti</span>
            <span style="width:auto;font-size:12px;color:#00ff00;">Glazers</span>
            <span style="width:auto;font-size:12px;color:#ff6000;">Limited</span>
          </span>
          <span style="color:#000;width:auto;font-size:12px;">PO Box 14-00623</span>
          <span style="color:#000;width:auto;font-size:12px;">Nairobi, Kenya.</span>
          <span style="color:#000;width:auto;font-size:12px;">Tel: 0714 452 313</span>
          <span style="color:#000;width:auto;font-size:12px;">info@maruti.co.ke</span>
          <span style="color:#000;width:auto;font-size:12px;">P051354459I</span>
          <span class="border border-top-0 border-left-0 border-right-0 border-lg border-dark w-25"></span>
          <span style="color:#000;width:auto;font-size:12px;">Mombasa Road, Kaysalt Complex Godown B14</span>
          <span style="color:#000;width:auto;font-size:12px;">P.O Box 14-00623 Nairobi, Kenya</span>
        </div>
        <div class="col-auto d-flex flex-column text-nowrap" style="color:#000;border:1px solid #ff6600;height:max-content;">
          <span class="font-weight-bold" style="width:auto;font-size:12px;">CUSTOMER: {{ $creditNote->customer->customer_name }}</span>
          <span style="color:#000;width:auto;font-size:12px;">PIN: {{ $creditNote->customer->kra_pin }}</span>
          <span style="color:#000;width:auto;font-size:12px;">EMAIL: {{ $creditNote->customer->contact_person_email }}</span>
          <span style="color:#000;width:auto;font-size:12px;">TEL: {{ $creditNote->customer->contact_number }}</span>
          <span style="width:auto;font-size:12px">
            NO: <b>{{ $creditNote->credit_note_prefix }}{{ $creditNote->credit_note_suffix }}</b>
          </span>
          <span style="width:auto;font-size:12px">
            DATE: {{ date('d-m-Y',strtotime($creditNote->record_date)) }}
          </span>
          <span style="width:auto;font-size:12px">
            GRAND TOTAL: <span id="grandTotal">{{ $creditNote->sum_grandtotal }}</span>
          </span>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <table class="col-sm-11 mx-auto" style="border-collapse:collapse;font-size:12px;">
        <thead style="color:#000;">
          <th style="border:1px solid #ff6600;padding:5px;">Code</th>
          <th style="border:1px solid #ff6600;padding:5px;">Color/Name</th>
          <th style="border:1px solid #ff6600;padding:5px;">Quantity</th>
          <th style="border:1px solid #ff6600;padding:5px;">KG</th>
          <th style="border:1px solid #ff6600;padding:5px;">@</th>
          <th style="border:1px solid #ff6600;padding:5px;">Amount</th>
        </thead>
        <tbody>
          @foreach($creditNote->creditnoteitems as $item)
          <tr style="color:#000;">
            @if($item->powder_id)
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->powder->powder_code }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->powder->powder_color }}</td>
            @elseif($item->inventory_item_id)
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->inventoryitem->item_code }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->inventoryitem->item_name }}</td>
            @endif


            @if($item->powder_id)
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->quantity }}</td>
            @else
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->quantity }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            @endif
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($item->unit_price_without_vat,2) }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($item->sub_total,2) }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="border-right: 1px solid transparent;">
            <td style="color:#000;text-align:center;padding:5px;border-left: 1px solid transparent;" class="font-weight-bold" colspan="3"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">
              SUB TOTAL
            </td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($creditNote->sum_subtotal,2) }}</td>
          </tr>
          <tr>
            <td style="border-right:1px solid transparent #ff6600;color:#000;text-align:center;padding:5px;border-left: 1px solid transparent;" class="font-weight-bold" colspan="3"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">VAT</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($creditNote->sum_vataddition,2) }}</td>
          </tr>
          <tr>
            <td style="border-right:1px transparent #ff6600;color:#000;text-align:center;padding:5px;border-left: 1px solid transparent;border-bottom: 1px solid transparent;" class="font-weight-bold" colspan="3"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">GRAND TOTAL</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ $creditNote->sum_grandtotal }}</td>
            <script>
              document.querySelector('#grandTotal').innerHTML = '{{ $creditNote->sum_grandtotal }}';
            </script>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="row" style="font-size:12px;">
      <div class="col-sm-11">
        <div class="p-3 ml-3" style="color:#000;">
          <p class="text-bold">Memo</p>
          <div class="">
            {{ $creditNote->memo }}
          </div>
        </div>
      </div>
    </div>

    <div style="position: absolute;bottom: 0cm;left: 0cm;right: 0cm;height: auto;line-height: 20px;">
      <div class="text-center d-flex justify-content-center align-content-center font-10">
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

    function printDirectInfo() {
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
        $.notify({
          icon: "tim-icons ui-1_bell-53",
          message: 'Email needs to be filled'
        }, {
          type: 'danger'
        });
        return false;
      }

      if (!email.checkValidity()) {
        $.notify({
          icon: "tim-icons ui-1_bell-53",
          message: 'Email entered failed verification check'
        }, {
          type: 'danger'
        });
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
          scale: 7
        },
      });

      const myPdf = await worker.outputPdf('datauristring', documentName);
      const preBlob = dataURItoBlob(myPdf);
      const file = new File([preBlob], documentName, {
        type: 'application/pdf'
      });

      const message = "<p>Please find the LPO attached: " + documentName.split('.')[0] + " </p>";

      const subject = "LPO " + documentName;

      const result = await sendFile(file, email.value, message, subject);

      if (result == 'error' || !result.status) {
        console.log(result);
        $.notify({
          icon: "tim-icons ui-1_bell-53",
          message: 'Failed to send. Please retry'
        }, {
          type: 'danger'
        });
      } else {
        console.log(result);
        $.notify({
          icon: "tim-icons ui-1_bell-53",
          message: 'Email sent!'
        }, {
          type: 'success'
        });
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