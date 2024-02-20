
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Stock Out',
'bootstrapselect' => true,
]
)

<body class="theme-green">
@include('universal-layout.spinner')

@include('universal-layout.system-sidemenu',
[
'slug' => '/inventory'
]
)
<section class="content home">
    <div class="container-fluid">
  <div class="wrapper">
    <div class="main-panel">

      <div class="content">

        <form class="" onsubmit="showSpinner(event)" action="{{ route('stock_out_inventory') }}" method="post" autocomplete="off">
          @csrf

          <div class="row">

            <div class="col-sm-6">
              <label>Done By</label>
              <input type="hidden" name="done_by" value="{{ $currentlyLogged->id }}">
              <input type="text" readonly name="username" class="form-control" value="{{ $currentlyLogged->username }}">
            </div>

            <div class="col-sm-6">
              <label>Responsible</label>
              <select class="form-control searchable-select" name="responsible">
                @foreach($users as $systemUser)
                  @if($systemUser->id != $currentlyLogged->id)
                  <option value="{{ $systemUser->id }}">{{ $systemUser->username }}</option>
                  @endif
                @endforeach
              </select>
            </div>

          </div>

          <div class="row">
            <div class="col-sm-6">
              <label>Note</label>
              <input type="text" class="form-control" name="note">
            </div>
          </div>


          <div class="row mt-3 pl-4 w-80" style="overflow-x: auto;display:block;overflow:initial;">
            <table class="table table-bordered col w-70 mx-auto">
              <th class="p-0 border">Item</th>
              <th class="p-0 border">Current Quantity/Weight</th>
              <th class="p-0 border">Quantity/Weight Removed</th>
              <th class="p-0 border">Action</th>
              <tbody class="item-list">
                <tr>
                  <td>
                    <input type="hidden" name="inventory_type[]" value="">
                    <input type="hidden" name="item_name[]" value="">
                    <input type="hidden" name="item_description[]" value="">
                    <input type="hidden" name="item_serial[]" value="">
                    <input type="hidden" name="warehouse_id[]" value="">
                    <input type="hidden" name="bin_id[]" value="">
                    <select style="width:200px;" name="item_id[]" class="searchable-select item-list-dropdown" onchange="prefillRowStockOut(this)" data-live-search="true">
                      <option disabled selected>Choose from inventory</option>
                      <optgroup label="POWDER">
                        <?php
                          foreach ($powderInventory as $powderItem) {
                        ?>
                          <option value="<?php echo $powderItem->item_id ?>"
                            attr-data-description="<?php echo $powderItem->powder_description ?>"
                            attr-data-code="<?php echo $powderItem->powder_code ?>"
                            attr-data-cost="<?php echo $powderItem->standard_cost ?>"
                            attr-warehouse-id=<?php echo $powderItem->warehouse_id ?>
                            attr-bin-id="<?php echo $powderItem->bin_id ?>"
                            attr-data-name ="<?php echo $powderItem->powder_color ?>"
                            attr-data-level = "<?php echo $powderItem->total_weight ?>"
                            attr-data-serial = "<?php echo $powderItem->serial_no ?>"
                             >
                            <?php echo $powderItem->powder_color ?>(<?php echo $powderItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>
                      </optgroup>

                      <optgroup label="HARDWARE">
                        <?php
                          foreach ($hardwareInventory as $hardwareItem) {
                        ?>
                          <option value="<?php echo $hardwareItem->item_id ?>"
                            attr-data-description="<?php echo $hardwareItem->item_description ?>"
                            attr-data-code="<?php echo $hardwareItem->item_code ?>"
                            attr-data-cost="<?php echo $hardwareItem->standard_cost ?>"
                            attr-warehouse-id=<?php echo $hardwareItem->warehouse_id ?>
                            attr-bin-id="<?php echo $hardwareItem->bin_id ?>"
                            attr-data-name ="<?php echo $hardwareItem->item_name ?>"
                            attr-data-level="<?php echo $hardwareItem->total_quantity ?>"
                            attr-data-serial="<?php echo $hardwareItem->serial_no ?>"
                             >
                            <?php echo $hardwareItem->item_name ?>(<?php echo $hardwareItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>
                      </optgroup>

                      <optgroup label="ALUMINIUM">
                        <?php
                          foreach ($aluminiumInventory as $aluminiumItem) {
                        ?>
                          <option value="<?php echo $aluminiumItem->item_id ?>"
                            attr-data-description="<?php echo $aluminiumItem->item_description ?>"
                            attr-data-code="<?php echo $aluminiumItem->item_code ?>"
                            attr-data-cost="<?php echo $aluminiumItem->standard_cost ?>"
                            attr-warehouse-id="<?php echo $aluminiumItem->warehouse_id ?>"
                            attr-bin-id="<?php echo $aluminiumItem->bin_id ?>"
                            attr-data-name ="<?php echo $aluminiumItem->item_name ?>"
                            attr-data-level="<?php echo $aluminiumItem->total_quantity ?>"
                            attr-data-serial="<?php echo $hardwareItem->serial_no ?>"
                            >
                            <?php echo $aluminiumItem->item_name ?>(<?php echo $aluminiumItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>
                      </optgroup>

                    </select>

                  </td>

                  <td>
                    <input type="text" readonly name="current_quantity[]" value="">
                  </td>

                  <td>
                    <input type="number" min="1" name="item_qty[]" value="">
                  </td>

                  <td>
                    <button type="button" name="button" class="btn btn-danger remove-btn" onclick="removeRow(this)">REMOVE</button>
                  </td>

                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="100%">
                    <button type="button" name="button" onclick="addItemRow()" class="btn btn-danger">ADD ITEM</button>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <button type="submit" name="submit_btn" value="CREATE STOCK OUT" class="btn btn-success text-white w-100">CREATE STOCK OUT</button>


        </form>
      </div>
    </div>
  </div>
    </div>
</section>

        <script type="text/javascript">
          function filterItems(selectElement) {
            console.log(selectElement.value);
            const removeBtns = document.querySelectorAll('.remove-btn');

            // if there is a change in warehouses, reset the table
            if (removeBtns.length > 1) {
              [...removeBtns].forEach((removeBtn) => {
                removeBtn.click();
              })
            }

            const itemsDropdown = document.querySelectorAll('.item-list-dropdown');
            [...itemsDropdown].forEach((itemDropdown) => {
              $(itemDropdown).select2('destroy');
              itemDropdown.disabled = false;
              const options = itemDropdown.querySelectorAll('option');
              [...options].forEach((option) => {
                if (option.getAttribute('attr-current-warehouse-id') != selectElement.value) {
                  option.disabled = true;
                }else {
                  option.disabled = false;
                }
              })
              $(itemDropdown).select2();
            })
          }

          function calculateRemaining(inputElement) {
            const itemRow = inputElement.parentElement.parentElement;
            const currentQuantity = itemRow.querySelector('input[name="current_quantity[]"]');
            const remainingQty = itemRow.querySelector('input[name="item_qty_remaining[]"]');


            remainingQty.value = parseInt(currentQuantity.value) - parseInt(inputElement.value);
            if (inputElement.value == '') {
              remainingQty.value = parseInt(currentQuantity.value) - 0;
            }


            if (remainingQty.value < parseInt(inputElement.getAttribute('attr-min-threshold'))) {
              $.notify({
                      icon: "tim-icons ui-1_bell-53",
                      message: 'Alert! This will take the item quantity below its minimum threshold quantity of ' + inputElement.getAttribute('attr-min-threshold')
                    }, {
                        type: 'danger'
                      });
            }
          }
        </script>

      </div>

      @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'bootstrapselect' => true,
   
  'tableAction' => true,
  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')
