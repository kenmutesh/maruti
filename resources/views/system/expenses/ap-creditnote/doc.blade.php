@include('universal-layout.header', ['pageTitle' => 'Aprotec | AP Credit Note Document'])
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style media="screen">
  @font-face {
    font-family: 'Verdana';
    src: url('/assets/fonts/verdana.ttf');
  }
  *{
    font-family: 'Verdana';
  }
</style>
<body class="bg-secondary">
  @include('universal-layout.spinner')

  <div class="row d-flex justify-content-around w-75 mx-auto">
    <button type="button" name="button" onclick="printInfo('AP Credit Note:{{ $creditNote->date_created }}.pdf')" class="btn btn-warning col-sm-3">
      SAVE TO LOCAL
    </button>

    <button type="button" name="button" onclick="printDirectInfo('Credit Note.pdf')" class="btn btn-warning col-sm-3">
      PRINT DIRECTLY
    </button>


    <div class="dropdown">
      <button type="button" class="btn btn-info dropdown-toggle d-flex align-items-center" class="btn btn-warning col-sm-3" data-toggle="dropdown">
        SEND DOCUMENT AS EMAIL
      </button>
      <div class="dropdown-menu p-4">
        <div class="form-group">
          <label for="">Email</label>
          <input type="email" name="email" required placeholder="Please insert email">
        </div>
        <button type="button" name="button" class="btn btn-info" onclick="sendPDF(this, 'AP Credit Note:{{ $creditNote->date_created }}.pdf')">
          SEND DOCUMENT
        </button>
      </div>
    </div>
  </div>

<div class="col-sm-12 mt-3 mx-auto h-100" style="width:100%;background:white;min-height:29cm;" id="documentFrame" style="background:white;font-family:'Helvetica';font-weight: lighter;">
  <h3 class="text-center">AP CREDIT NOTE</h3>

  <div class="row">
    <div class="col-sm-11 d-flex justify-content-between">
      <div class="col-sm-6 d-flex flex-column ml-2">
        <img src="/assets/img/maruti.png" width="200px" alt="">
        <span style="color:#000;width:auto;font-size:14px;">Maruti Glazers Ltd</span>
        <span style="color:#000;width:auto;font-size:14px;">PO Box 14-00623</span>
        <span style="color:#000;width:auto;font-size:14px;">Nairobi, Kenya.</span>
        <span style="color:#000;width:auto;font-size:14px;">Tel: 0731 191 444</span>
        <span style="color:#000;width:auto;font-size:14px;">info@maruti.co.ke</span>
      </div>
      <div class="col-sm-4 d-flex flex-column" style="margin-right: -6%;">
        <span style="width:auto;font-size:14px">
          Date Created: {{ date('Y-m-d',strtotime( $creditNote->date_created )) }}
        </span>
        <span style="width:auto;font-size:14px">
          Supplier: {{ $creditNote->supplier->supplier_name }}
        </span>
        <span style="width:auto;font-size:14px">
          Created by: {{ $creditNote->systemuser->username }}
        </span>
        <span style="width:auto;font-size:14px">
          Grand Total: {{ number_format($creditNote->grand_total, 2) }}
        </span>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <table class="col-sm-11 mx-auto" style="border-collapse:collapse;font-size:14px;">
      <thead style="">
        <th style="border:1px solid #ff6600;">Item Name/Color</th>
        <th style="border:1px solid #ff6600;">Item Description</th>
        <th style="border:1px solid #ff6600;">Quantity</th>
        <th style="border:1px solid #ff6600;">KG</th>
        <th style="border:1px solid #ff6600;">Unit Cost</th>
        <th style="border:1px solid #ff6600;">Amount</th>
      </thead>
      <tbody>
        <?php
          $itemList = json_decode($creditNote->item_list);
          $vatAddition = 0;

          $subTotal = 0;
          foreach ($itemList as $item) {
            $vatPercentage = ($item->item_tax ?? 0)/100;
        ?>
        <tr style="color:#000;">
          <td style="border:1px solid #ff6600;padding:5px;">{{ $item->item_name }}</td>
          <td style="border:1px solid #ff6600;padding:5px;">{{ $item->item_description }}</td>
          @if( $item->inventory_type == 'POWDER')
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->item_kg }}</td>
            <?php
                $subTotal = $subTotal + ($item->amount / (1 + $vatPercentage));
                $vatAddition = $vatAddition + (($item->amount * $vatPercentage) / (1 + $vatPercentage));
            ?>
          @else
            <td style="border:1px solid #ff6600;padding:5px;">{{ $item->item_qty }}</td>
            <td style="border:1px solid #ff6600;padding:5px;">-</td>
            <?php
                $subTotal = $subTotal + ($item->amount / (1 + $vatPercentage));
                $vatAddition = $vatAddition + (($item->amount * $vatPercentage) / (1 + $vatPercentage));
            ?>
          @endif
          <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($item->unit_cost, 2) }}</td>
          <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($item->amount, 2) }}</td>
        </tr>

        <?php
          }
        ?>
      </tbody>
      <tfoot>
        <tr>
            <td style="border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="3"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">Sub Total</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($subTotal, 2) }}</td>
        </tr>
        <tr>
            <td style="border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="3"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">VAT({{ $vatPercentage }} %)</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;">{{ number_format($vatAddition, 2) }}</td>
        </tr>
        <tr>
          <td tyle="border:1px solid #transparent;padding:5px;" colspan="3"></td>
          <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" colspan="2">Grand Total</td>
          <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($creditNote->grand_total, 2) }}</td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="row" style="font-size:14px;">
    <div class="col-sm-11">
      <div class="p-3 ml-3" style="color:#000;">
        <p class="text-bold">Memo</p>
        <div class="">
          {{ $creditNote->memo }}
        </div>
      </div>
    </div>
  </div>

  <div style="position: absolute;bottom: 0cm;left: 0cm;right: 0cm;height: auto;line-height: 35px;">
    <div class="text-center d-flex justify-content-center align-content-center">
      <p>This is a document generated by the <img src="/assets/img/aprotec-ico.png" alt="Aprotec" width="30px"> system</p>
    </div>
  </div>
</div>

<script src="/assets/js/plugins/html2pdf.bundle.min.js"></script>

<script type="text/javascript">

async function printInfo(filename) {
  const worker = html2pdf();

  worker.set({
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
    html2canvas:  { scale: 2 },
  });

  await worker.from(document.querySelector('#documentFrame'));

  let myPdf = await worker.save(filename);
}

function printDirectInfo() {
      html2pdf().from(document.querySelector('#documentFrame')).toPdf().get('pdf').then(function (pdfObj) {
          // pdfObj has your jsPDF object in it, use it as you please!
          pdfObj.autoPrint();
          window.open(pdfObj.output('bloburl'), '_blank');
      });
    }


async function sendPDF( btnElement, documentName) {
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
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
    html2canvas:  { scale: 2 },
  });

  const myPdf = await worker.outputPdf('datauristring', documentName);
  const preBlob = dataURItoBlob(myPdf);
  const file = new File([preBlob], documentName, {type: 'application/pdf'});

  const message = "<p>Please find the AP credit note attached: "+ documentName.split('.')[0] + " </p>";

  const subject = "AP Credit Note " + documentName;

  const result = await sendFile(file, email.value , message, subject);

  if (result == 'error' || !result.status) {
    console.log(result);
    $.notify({
            icon: "tim-icons ui-1_bell-53",
            message: 'Failed to send. Please retry'
          }, {
              type: 'danger'
            });
  }else {
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

    return new Blob([ia], {type:mimeString});
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
