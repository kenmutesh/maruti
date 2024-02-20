@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Cash Sale',
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
    <button type="button" name="button" onclick="printInfo('Cash Sale:{{ $cashsale->date_created }}.pdf')" class="btn btn-warning col-sm-3">
      SAVE TO LOCAL
    </button>

    <button type="button" name="button" onclick="printDirectInfo('{{ $cashsale->cash_sale_no }}.pdf')" class="btn btn-warning col-sm-3">
      PRINT DIRECTLY
    </button>


    <div class="dropdown">
      <button type="button" class="btn btn-warning dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
        SEND DOCUMENT AS EMAIL
      </button>
      <div class="dropdown-menu p-4">
        <div class="form-group">
          <label for="">Email</label>
          <input type="email" name="email" required placeholder="Please insert email">
        </div>
        <button type="button" name="button" class="btn btn-info" onclick="sendPDF(this, 'Cash Sale:{{ $cashsale->created_at }}.pdf')">
          SEND DOCUMENT
        </button>
      </div>
    </div>
  </div>

  <div class="col-sm-12 mt-3 mx-auto h-100" style="width:100%;background:white;min-height:29cm;" id="documentFrame" style="background:white;font-family:'Helvetica';font-weight: lighter;">
    @if($cashsale->cancelled_at)
    <div class="position-absolute h-100 w-100 d-flex justify-content-center align-items-center" style="background-color: #ff36364d !important;">
      <h1 class="display-1" style="transform: rotate(45deg);">Cancelled</h1>
    </div>
    @endif
    <h3 class="text-center">CASH SALE</h3>

    <div class="row">
      <div class="col-sm-11 d-flex justify-content-between">
        <div class="col-sm-6 d-flex flex-column ml-2">
          <img src="/assets/img/maruti-square.png" width="120px" alt="">
          <span class="font-weight-bold" style="width:auto;font-size:12px;">
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
          <span style="color:#000;width:auto;font-size:12px;" class="text-nowrap">Mombasa Road, Kaysalt Complex, Godown B14</span>
        </div>
        <div class="col-auto d-flex flex-column text-nowrap" style="color:#000;margin-right: -6%;border:1px solid #ff6600;height:max-content;">
          <span class="w-auto" style="font-size:12px;">
            TO: <b>{{ $cashsale->customer->customer_name }} {{ $cashsale->coatingjobs[0]->cash_sale_name }}</b>
          </span>
          <span style="width:auto;font-size:12px">
            PIN: {{ $cashsale->customer->kra_pin ?? '' }}
          </span>
          <span class="w-auto" style="font-size:12px;">
            EMAIL: {{ $cashsale->customer->contact_person_email }}
          </span>
          <span class="w-auto" style="font-size:12px;">
            TEL: {{ $cashsale->customer->contact_number }}
          </span>
          <span class="font-weight-bold" style="width:auto;font-size:12px">
            CASH SALE NO:
            @if($cashsale->external)
            {{ $cashsale->ext_cash_sale_prefix }}{{ $cashsale->ext_cash_sale_suffix }}
            @else
            {{ $cashsale->cash_sale_prefix }}{{ $cashsale->cash_sale_suffix }}
            @endif
          </span>
          <span class="font-weight-bold" style="width:auto;font-size:12px">
            CU INV NO: {{ $cashsale->cu_number_prefix }}{{ $cashsale->cu_number_suffix }}
          </span>
          <span style="width:auto;font-size:12px">
            DATE: {{ date('d-m-Y',strtotime( $cashsale->created_at )) }}
          </span>
        </div>
      </div>
    </div>


    <div class="row mt-4">
      <table class="col-sm-11 mx-auto" style="border-collapse:collapse;font-size:12px;">
        <thead style="color:#000;" class="text-center">
          <th style="border:1px solid #ff6600;border-top:2px solid #ff6600;">Color/Name</th>
          <th style="border:1px solid #ff6600;border-top:2px solid #ff6600;">Quantity</th>
          <th style="border:1px solid #ff6600;border-top:2px solid #ff6600;">UoM</th>
          <th style="border:1px solid #ff6600;border-top:2px solid #ff6600;">KG</th>
          <th style="border:1px solid #ff6600;border-top:2px solid #ff6600;">@</th>
          <th style="border:1px solid #ff6600;border-top:2px solid #ff6600;border-right:2px solid #ff6600;">Amount</th>
        </thead>
        <tbody>
          @forelse($cashsale->coatingjobs as $coatingjob)
            @foreach($coatingjob->marutiitems as $marutiitem)
                @if($loop->last)
                  @if($coatingjob->powder)
                    @if($marutiitem->inventory_item_id)
                      <tr style="color:#000;">
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ $marutiitem->inventoryitem->item_name }} ({{ $coatingjob->powder->powder_color }})
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ intval($coatingjob->powder_estimate) + intval($coatingjob->goods_weight) }}
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                      </tr>
                    @endif

                    @if($marutiitem->powder_id)
                      <tr style="color:#000;">
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ $marutiitem->powder->powder_color }} ({{ $coatingjob->powder->powder_color }})
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ intval($coatingjob->powder_estimate) + intval($coatingjob->goods_weight) }}
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                      </tr>
                    @endif

                    @if($marutiitem->custom_item_name)
                      <tr style="color:#000;">
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ $marutiitem->custom_item_name }} ({{ $coatingjob->powder->powder_color }})
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ intval($coatingjob->powder_estimate) + intval($coatingjob->goods_weight) }}
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                      </tr>
                    @endif
                  @else
                    @if($marutiitem->inventory_item_id)
                      <tr style="color:#000;">
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->inventoryitem->item_name }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">0</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                      </tr>
                    @endif

                    @if($marutiitem->powder_id)
                      <tr style="color:#000;">
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->powder->powder_color }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">0</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                      </tr>
                    @endif

                    @if($marutiitem->custom_item_name)
                      <tr style="color:#000;">
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->custom_item_name }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">0</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                      </tr>
                    @endif
                  @endif
                @else
                  @if($marutiitem->inventory_item_id)
                    <tr style="color:#000;">
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->inventoryitem->item_name }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">0</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                    </tr>
                  @endif

                  @if($marutiitem->powder_id)
                    <tr style="color:#000;">
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->powder->powder_color }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">0</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                    </tr>
                  @endif

                  @if($marutiitem->custom_item_name)
                    <tr style="color:#000;">
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->custom_item_name }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->quantity }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $marutiitem->uom }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">0</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->unit_price_without_vat, 2) }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($marutiitem->sub_total, 2) }}</td>
                    </tr>
                  @endif
                @endif
              @endforeach

              @foreach($coatingjob->aluminiumitems as $aluminiumitem)
                <tr style="color:#000;">
                  @if($loop->last)
                      @if($coatingjob->powder)
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ $aluminiumitem->item_name }} ({{ $coatingjob->powder->powder_color }})
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">
                          {{ $aluminiumitem->item_kg }}
                        </td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->sub_total, 2) }}</td>
                      @else
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->item_name }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->quantity }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->uom }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->item_kg }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->unit_price_without_vat, 2) }}</td>
                        <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->sub_total, 2) }}</td>
                      @endif
                  @else
                    <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->item_name }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->item_kg }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ $aluminiumitem->uom }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">0</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->unit_price_without_vat, 2) }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($aluminiumitem->sub_total, 2) }}</td>
                  @endif
                </tr>
              @endforeach

              @foreach($coatingjob->steelitems as $steelitem)
                <tr style="color:#000;">
                  @if($loop->last)
                    @if($coatingjob->powder)
                      <td style="border:1px solid #ff6600;padding:5px;">
                        {{ $steelitem->item_name }} ({{ $coatingjob->powder->powder_color }})
                      </td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->quantity }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->uom }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">
                        {{ intval($coatingjob->powder_estimate) + intval($coatingjob->goods_weight) }}
                      </td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->unit_price_without_vat, 2) }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->sub_total, 2) }}</td>
                    @else
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->item_name }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->quantity }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->uom }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">0</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->unit_price_without_vat, 2) }}</td>
                      <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->sub_total, 2) }}</td>
                    @endif
                  @else
                    <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->item_name }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->quantity }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ $steelitem->uom }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">0</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->unit_price_without_vat, 2) }}</td>
                    <td style="border:1px solid #ff6600;padding:5px;">{{ number_format($steelitem->sub_total, 2) }}</td>
                  @endif
                </tr>
              @endforeach
          @empty
          <tr>
            <td style="border:1px solid #ff6600;padding:5px;" class="font-weight-bold" colspan="100%">
              Pre-system invoice - No particulars
            </td>
          </tr>
          @endforelse
        </tbody>
        <tfoot>
          @if($cashsale->discount > 0)
          <tr>
            <td colspan="3" style="border:1px solid transparent;border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">DISCOUNT</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;border-right:2px solid #ff6600;">{{ number_format($cashsale->discount, 2) }}</td>
          </tr>
          @endif
          <tr>
            <td colspan="3" style="border:1px solid transparent;border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;border-top:1px solid #ff6600;" class="font-weight-bold"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">SUB TOTAL</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;border-right:2px solid #ff6600;">{{ number_format($cashsale->sub_total, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3" style="border:1px solid transparent;border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold" colspan="2">VAT</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;border-right:2px solid #ff6600;">{{ number_format(($cashsale->vat_addition), 2) }}</td>
          </tr>
          <tr>
            <td colspan="3" style="border:1px solid transparent;border-right:1px solid #ff6600;color:#000;text-align:center;padding:5px;" class="font-weight-bold"></td>
            <td style="border:1px solid #ff6600;color:#000;text-align:center;padding:5px;border-bottom:2px solid #ff6600;" class="font-weight-bold" colspan="2">GRAND TOTAL</td>
            <td style="border:1px solid #ff6600;color:#000;padding:5px;border-right:2px solid #ff6600;border-bottom:2px solid #ff6600;">{{ number_format(($cashsale->grand_total), 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="row mt-1" style="color:#000;">
        <div class="col-sm-11">
          <div class="col-sm-8 d-flex flex-column ml-3">
            <div class="border border-top-0 border-left-0 border-right-0 border-lg border-dark w-50 mt-1">
              <span style="font-size:12px;">Approved By: Kiran K. Mavji</span>
            </div>
            <div class="border border-top-0 border-left-0 border-right-0 border-lg border-dark w-50 mt-1">
              <span style="font-size:12px;">Created By: {{ $cashsale->creator->username ?? '' }}</span>
            </div>
            <div class="border border-top-0 border-left-0 border-right-0 border-lg border-dark w-50 mt-1">
              <span style="font-size:12px;">Received By:</span>
            </div>
          </div>
        </div>
      </div>

    <div style="position: absolute;bottom: 0cm;left: 0cm;right: 0cm;height: auto;line-height: 15px;">
      <div class="text-center d-flex justify-content-center align-content-center font-10">
        <p class="m-0">This is a document generated by the www.aprotecsystem.com system</p>
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
          scale: 8
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