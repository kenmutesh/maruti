@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Statements',
]
)

<body class="theme-green">
  <style media="screen">
    @font-face {
      font-family: 'Verdana';
      src: url('/assets/fonts/verdana.ttf');
    }

    * {
      font-family: 'Verdana';
    }
  </style>
  @include('universal-layout.spinner')

  <div class="row d-flex justify-content-around w-75 mx-auto">
    <button type="button" name="button" onclick="printInfo('Statement:{{ $supplier->supplier_name }}.pdf')" class="btn btn-info col-sm-3">
      SAVE TO LOCAL
    </button>


    <div class="dropdown">
      <button type="button" class="btn btn-info dropdown-toggle d-flex align-items-center" class="btn btn-info col-sm-3" data-toggle="dropdown">
        SEND DOCUMENT AS EMAIL
      </button>
      <div class="dropdown-menu" style="width:25rem;left:-10rem !important;">
        <div class="col-12 p-2 email-send">

          <div class="form-group">
            <label for="">Message Template</label>
            <textarea class="form-control border" rows="7" name="message">Dear {{ $supplier->supplier_name }},
Your statement is attached. Please remit payment at your earliest convenience.Thank you for your business - we appreciate it very much.
Sincerely,
MARUTI GLAZERS LTD</textarea>
          </div>

          <div class="form-group">
            <label for="">Main Email:</label>
            <input type="email" name="main_email" required value="{{ $supplier->supplier_email }}" placeholder="Please insert email" class="form-control">
          </div>
          <div class="form-group cc-parent">
            <label for="">CC</label>
            <input type="email" name="cc_email" required placeholder="Please insert email" class="form-control mb-1">
          </div>
          <button class="btn btn-sm btn-success col-12" type="button" onclick="addInput('.cc-parent')">Add+</button>
          <div class="form-group bcc-parent">
            <label for="">BCC</label>
            <input type="email" name="bcc_email" required placeholder="Please insert email" class="form-control mb-1" value="info@maruti.co.ke">
          </div>
          <button class="btn btn-sm btn-success col-12" type="button" onclick="addInput('.bcc-parent')">Add+</button>
          <button type="button" name="button" class="btn btn-info" onclick="sendPDF(this, 'Statement:{{ $supplier->supplier_name }}.pdf')">
            SEND DOCUMENT
          </button>
        </div>
        <script>
          function addInput(parentSelector) {
            const input = document.querySelector(`${parentSelector} > input`);
            const clone = input.cloneNode(true);
            clone.value = '';
            document.querySelector(`${parentSelector}`).appendChild(clone);
          }
        </script>
      </div>
    </div>
  </div>

  <div class="col-sm-12 mt-3 mx-auto h-100" style="width:100%;background:white;min-height:29cm;" id="documentFrame" style="background:white;font-family:'Helvetica';font-weight: lighter;">
    <h4 class="text-center m-0">STATEMENT</h4>

    <div class="row">
      <div class="col-sm-11 d-flex justify-content-between">
        <div class="col-sm-6 d-flex flex-column ml-2">
          <img src="/assets/img/maruti-square.png" width="95px" alt="">
          <span style="color:#000;width:auto;font-size:10px;">Maruti Glazers Ltd</span>
          <span style="color:#000;width:auto;font-size:10px;">PO Box 14-00623</span>
          <span style="color:#000;width:auto;font-size:10px;">Nairobi, Kenya.</span>
          <span style="color:#000;width:auto;font-size:10px;">Tel: 0731 191 444</span>
          <span style="color:#000;width:auto;font-size:10px;">info@maruti.co.ke</span>
          <span style="color:#000;width:auto;font-size:10px;" class="text-nowrap">Mombasa Road, Kaysalt Complex, Godown B14</span>
        </div>
        <div class="col-sm-5 d-flex flex-column" style="color:#000;margin-right: -6%;border:1px solid #ff6600;height:fit-content;">
          <span style="width:auto;font-size:10px">
            <b>TO: {{ $supplier->supplier_name }}</b>
          </span>
          <span style="width:auto;font-size:10px">
            EMAIL: {{ $supplier->supplier_email }}
          </span>
          <span style="width:auto;font-size:10px">
            TEL: {{ $supplier->supplier_mobile }}
          </span>
          <span style="width:auto;font-size:10px">
            @if(isset($statementDate))
            DATE: {{ date('d/m/Y', strtotime($statementDate)) }}
            @else
            DATE: {{ date('d/m/Y', time()) }}
            @endif
          </span>
        </div>
      </div>
    </div>


    <div class="row mt-4 pb-5">
      <table class="col-sm-11 mx-auto" style="border-collapse:collapse;font-size:10px;">
        <thead>
          <tr>
            <th colspan="4" style="border-right:1px solid #ff6600;"></th>
            <th style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              Amount Due
            </th>
            <th style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              Amount Enc
            </th>
          </tr>

          <tr>
            <th colspan="4" style="border-right:1px solid #ff6600;"></th>
            <th style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              KES <span id="amountDue">0</span>
            </th>
            <th style="border:1px solid #ff6600;color:#000;"></th>
          </tr>

          <tr style="border-bottom:1px solid #fff;">
            <th colspan="2" style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              Date
            </th>
            <th colspan="2" style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              Transaction
            </th>
            <th style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              Amount
            </th>
            <th style="border:1px solid #ff6600;color:#000;" class="text-center p-2">
              Balance
            </th>
          </tr>

        </thead>
        <tbody>
          <tr style="color:#000;">
            <td colspan="5" style="border:1px solid #ff6600;padding:5px;">
              Brought forward
            </td>
            <td style="border:1px solid #ff6600;padding:5px;">
              {{ number_format($broughtForward,2) }}
            </td>
          </tr>
          <?php

          $loopsAmount = $broughtForward;

          ?>
          @foreach($recordsArray as $record)
          <tr>
            <td colspan="2" style="border:1px solid #ff6600;padding:5px;">
              {{ date('d/m/Y', strtotime($record['date'])) }}
            </td>
            <td colspan="2" style="border:1px solid #ff6600;color: #000;">
              {{ $record['id'] }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;">
              {{ number_format($record['amount'], 2) }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;">
              <?php $loopsAmount = $loopsAmount + $record['amount']; ?>
              {{ number_format($loopsAmount,2) }}
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">CURRENT</td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">1-30 DAYS PAST DUE</td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">31-60 DAYS PAST DUE</td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">61-90 DAYS PAST DUE</td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">OVER 90 DAYS PAST DUE</td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">Amount Due</td>
          </tr>
          <tr>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              {{ number_format($supplier->singleDayPurchasesAmountDue($statementDate),2) }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              {{ number_format($supplier->dateRangePeriodPurchasesAmountDue($statementDate, 1, 30),2) }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              {{ number_format($supplier->dateRangePeriodPurchasesAmountDue($statementDate, 31, 61),2) }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              {{ number_format($supplier->dateRangePeriodPurchasesAmountDue($statementDate, 61, 91),2) }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              {{ number_format($supplier->singlePeriodOverPurchasesAmountDue($statementDate, 91),2) }}
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              KES <?php echo number_format($loopsAmount, 2) ?>
            </td>
            <script>
              document.querySelector('#amountDue').innerHTML = '<?php echo number_format($loopsAmount, 2) ?>';
            </script>
          </tr>

        </tfoot>
      </table>
    </div>

    <div style="position: absolute;bottom: 0cm;left: 0cm;right: 0cm;height: auto;line-height: 35px;" class="pt-3">
      <div class="text-center d-flex justify-content-center align-content-center">
        <p>This is a document generated by the <img src="/assets/img/aprotec-ico.png" alt="Aprotec" width="30px"> system</p>
      </div>
    </div>
  </div>
  @include('universal-layout.scripts',
  [
  'sweetalert' => true,
  ]
  )

  <script src="/assets/js/plugins/html2pdf.bundle.min.js"></script>

  <script type="text/javascript">
    function removeCCEmailField(btnElement) {
      btnElement.parentElement.remove();
    }

    async function printInfo(filename) {
      const worker = html2pdf();

      worker.set({
        pagebreak: {
          mode: ['avoid-all', 'css', 'legacy']
        },
        html2canvas: {
          scale: 3
        },
      });

      await worker.from(document.querySelector('#documentFrame'));

      let myPdf = await worker.save(filename);
    }


    async function sendPDF(btnElement, documentName) {
      const parentElement = document.querySelector('.email-send');
      const message = parentElement.querySelector('textarea').value;
      const email = document.querySelector('input[name="main_email"]');
      const carbonCopies = [];

      const carbonCopySection = document.querySelectorAll('.cc-parent > input');
      for (const emailInput of carbonCopySection) {
        if (emailInput.value != '') {
          carbonCopies.push(emailInput.value);
        }
      }

      const blindCarbonCopies = [];
      const blindCarbonCopySection = document.querySelectorAll('.bcc-parent > input');
      for (const emailInput of blindCarbonCopySection) {
        if (emailInput.value != '') {
          blindCarbonCopies.push(emailInput.value);
        }
      }

      if (email.value == '') {
        swal({
          title: 'Error',
          text: 'Main email needs to be filled',
          type: 'error',
          timer: 2500,
          buttons: false,
        });
        return false;
      }

      if (!email.checkValidity()) {
        swal({
          title: 'Error',
          text: 'Main email entered failed verification check',
          type: 'error',
          timer: 2500,
          buttons: false,
        });
        return false;
      }
      // reference to the previous html content
      const previousHTML = btnElement.innerHTML;
      btnElement.disabled = true;
      // show the spinner
      btnElement.innerHTML = '<span class="spinner-border text-dark"></span><span>SENDING</span>';

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

      const subject = "STATEMENT FROM MARUTI GLAZERS";

      const result = await sendFile(file, email.value, message, subject, carbonCopies, blindCarbonCopies);

      if (result == 'error' || !result.status) {
        console.log(result);
        swal({
          title: 'Error',
          text: 'Failed to send. Please retry',
          type: 'error',
          timer: 2500,
          buttons: false,
        });
      } else {
        console.log(result);
        swal({
          title: 'Success',
          text: 'Sent!',
          type: 'success',
          timer: 2500,
          buttons: false,
        });
      }
      btnElement.disabled = false;
      // show the spinner
      btnElement.innerHTML = previousHTML;
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
  </script>

  @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  ]
  )
  <script>
    $(".dropdown-menu").click(function(e) {
      e.stopPropagation();
    })
  </script>
  @include('universal-layout.footer')