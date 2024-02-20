<style media="screen">
    @font-face {
      font-family: 'Verdana';
      src: url('/assets/fonts/verdana.ttf');
    }

    * {
      font-family: 'Verdana';
    }
  </style>
  <div class="col-sm-12 mx-auto h-100" style="width:100%;background:white;min-height:29cm;" style="background:white;font-family:'Helvetica';font-weight: lighter;">
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
        </div>
        <div class="col-sm-5 d-flex flex-column" style="color:#000;margin-right: -6%;border:1px solid #ff6600;height:fit-content;">
          <span style="width:auto;font-size:10px">
            <b>Customer Name: {{ $customer->company }}</b>
          </span>
          <span style="width:auto;font-size:10px">
            Email: {{ $customer->contact_person_email }}
          </span>
          <span style="width:auto;font-size:10px">
            Number: {{ $customer->contact_number }}
          </span>
          <span style="width:auto;font-size:10px">
            @if(isset($statementDate))
              Date: {{ date('d/m/Y', strtotime($statementDate)) }}
            @else
              Date: {{ date('d/m/Y', time()) }}
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
            <?php
                $total = ($customer->dues['current'][0]->total ?? 0) + ($customer->dues['thirty_days'][0]->total ?? 0) + ($customer->dues['sixty_days'][0]->total ?? 0) + ($customer->dues['ninety_days'][0]->total ?? 0) + ($customer->dues['over_ninety_days'][0]->total ?? 0)
              ?>
              KES <?php echo number_format($total, 2) ?>
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

          $currentDay = time();
          $current = 0;

          foreach ($records as $singleRecordInfo) {
          ?>
            <tr style="color:#000;">
              <td colspan="2" style="border:1px solid #ff6600;padding:5px;">
                <?php echo date('d/m/Y', strtotime($singleRecordInfo['date'])) ?>
              </td>
              <td colspan="2" style="border:1px solid #ff6600;padding:5px;"><?php echo $singleRecordInfo['id'] ?></td>
              <?php
              if ($singleRecordInfo['type'] == 'Payment') {
              ?>
                <td style="border:1px solid #ff6600;padding:5px;">
                  <?php

                  echo number_format($singleRecordInfo['amount'], 2)

                  ?>
                </td>
              <?php
                $loopsAmount = $loopsAmount + $singleRecordInfo['amount'];
              } else if ($singleRecordInfo['type'] == 'Nullified') {
              ?>
                <td style="border:1px solid #ff6600;padding:5px;">
                  <?php

                  echo number_format($singleRecordInfo['amount'], 2)

                  ?>
                </td>
              <?php
                $loopsAmount = $loopsAmount + $singleRecordInfo['amount'];
              } elseif ($singleRecordInfo['type'] == 'Credit Note') {
              ?>
                <td style="border:1px solid #ff6600;padding:5px;">
                  <?php

                  echo '-' . number_format($singleRecordInfo['amount'], 2)

                  ?>
                </td>
              <?php
                $loopsAmount = $loopsAmount + $singleRecordInfo['amount'];
              } else {
              ?>
                <td style="border:1px solid #ff6600;padding:5px;">
                  <?php echo number_format($singleRecordInfo['amount'], 2) ?>
                </td>
            <?php
                $loopsAmount = $loopsAmount + $singleRecordInfo['amount'];
              }
              ?>
                <td style="border:1px solid #ff6600;padding:5px;"><?php echo number_format($loopsAmount, 2) ?></td>
              <?php              
            }
            ?>
            <script>
              document.querySelector('#amountDue').innerHTML = '<?php echo number_format($loopsAmount, 2) ?>';
            </script>
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
              <?php echo number_format(($customer->dues['current'][0]->total ?? 0), 2) ?>
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              <?php echo number_format(($customer->dues['thirty_days'][0]->total ?? 0), 2) ?>
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              <?php echo number_format(($customer->dues['sixty_days'][0]->total ?? 0), 2) ?>
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              <?php echo number_format(($customer->dues['ninety_days'][0]->total ?? 0), 2) ?>
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              <?php echo number_format(($customer->dues['over_ninety_days'][0]->total ?? 0), 2) ?>
            </td>
            <td style="border:1px solid #ff6600;color: #000;" class="text-center">
              KES <?php echo number_format($loopsAmount, 2) ?>
            </td>
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