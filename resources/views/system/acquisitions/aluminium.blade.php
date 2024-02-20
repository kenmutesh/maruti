@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Add Aluminium Acquisition',
'bootstrapselect' => true,
]
)

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/acquisitions'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

      <div class="content">

        <form class="" onsubmit="showSpinner(event)" action="{{ route('aluminium_acquisition') }}" method="post" autocomplete="off">
          @csrf

          <div class="row">

            <div class="col">
              <div class="form-group">
                <label for="supplier">Warehouse</label>
                @if(session()->get('auth_warehouse_uid') != 'N/A')
                  <select class="form-control searchable-select warehouse-select p-0" required name="main_warehouse_id" onchange="filterItems(this)" data-live-search="true" data-style="text-white">
                    <option value="" disabled selected>Choose A Warehouse</option>
                    @foreach($warehouses as $singleWarehouse)
                        <option value="{{ $singleWarehouse->id }}">
                          {{ $singleWarehouse->warehouse_name }} - ({{ $singleWarehouse->location->location_name }})
                        </option>

                    @endforeach
                  </select>
                @else
                  <select class="form-control searchable-select warehouse-select p-0" required name="main_warehouse_id" onchange="filterItems(this)" data-live-search="true" data-style="text-white">
                    <option value="" disabled selected>Choose A Warehouse</option>
                    @foreach($warehouses as $singleWarehouse)
                      @if($singleWarehouse->location->location_status != 'ACTIVE')
                        continue;
                      @endif
                        <option value="{{ $singleWarehouse->id }}">
                          {{ $singleWarehouse->warehouse_name }} - ({{ $singleWarehouse->location->location_name }})
                        </option>
                    @endforeach
                  </select>
                @endif

              </div>
            </div>

            <div class="col">
              <label>Date</label>
              <input type="date" required name="record_date" class="form-control dark-text p-2">
            </div>

          </div>


          <div class="mt-3 pl-4 w-80 table-responsive" style="overflow-x: auto;display:block;overflow:initial;">
            <table class="table table-bordered w-70 mx-auto table-fixed-2">
              <th class="p-0 border">Item</th>
              <th class="p-0 border">Description for Acquisition</th>
              <th class="p-0 border">Current Quantity</th>
              <th class="p-0 border">Quantity</th>
              <th class="p-0 border">Remaining</th>
              <th class="p-0 border">Action</th>
              <tbody class="item-list">
                <tr>
                  <td>
                    <input type="hidden" name="item_name[]" value="">
                    <input type="hidden" name="item_serial[]" value="">
                    <input type="hidden" name="warehouse_id[]" value="">
                    <input type="hidden" name="bin_id[]" value="">
                    <select disabled style="width:200px;" name="item_id[]" class="searchable-select item-list-dropdown" onchange="prefillRowAcquisition(this)" data-live-search="true" data-style="text-white">
                      <option disabled selected>Choose from inventory</option>

                        <?php
                          foreach ($aluminiumInventory as $aluminiumItem) {
                        ?>
                          <option value="<?php echo $aluminiumItem->item_id ?>" attr-data-name="<?php echo $aluminiumItem->item_name ?>" attr-data-serial="<?php echo $aluminiumItem->serial_no ?>" attr-data-description="<?php echo $aluminiumItem->item_description ?>" attr-min-threshold="<?php echo $aluminiumItem->min_threshold ?>" attr-current-quantity="<?php echo $aluminiumItem->total_quantity ?>" attr-current-warehouse-id="<?php echo $aluminiumItem->warehouse_id ?>" attr-current-bin-id="<?php echo $aluminiumItem->bin_id ?>" data-token="<?php echo $aluminiumItem->item_name ?>">
                            <?php echo $aluminiumItem->item_name ?> (<?php echo $aluminiumItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>

                    </select>

                  </td>

                  <td>
                    <input type="text" class="w-100" name="item_description[]" value="">
                  </td>

                  <td>
                    <input type="text" class="w-100" readonly name="current_quantity[]" value="">
                  </td>

                  <td>
                    <input type="number" class="w-100" oninput="calculateRemaining(this)" min="1" name="item_qty[]" value="">
                  </td>

                  <td>
                    <input type="text" class="w-100" readonly name="item_qty_remaining[]" value="">
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

          <button type="submit" name="submit_btn" value="Create Purchase Order" class="btn btn-success text-white w-100">CREATE INVENTORY ACQUISITION</button>


        </form>
      </div>
        </div>
      </div>
    </div>
  </section>

        <script type="text/javascript">
          function filterItems(selectElement) {
            const removeBtns = document.querySelectorAll('.remove-btn');

            // if there is a change in warehouses, reset the table
            if (removeBtns.length > 1) {
              [...removeBtns].forEach((removeBtn) => {
                removeBtn.click();
              })
            }

            const itemsDropdown = document.querySelectorAll('.item-list-dropdown');
            [...itemsDropdown].forEach((itemDropdown) => {
              $(itemDropdown).selectpicker('destroy');
              itemDropdown.disabled = false;
              const options = itemDropdown.querySelectorAll('option');
              [...options].forEach((option) => {
                if (option.getAttribute('attr-current-warehouse-id') == selectElement.value) {
                  option.disabled = true;
                }else {
                  option.disabled = false;
                }
              })
              $(itemDropdown).selectpicker();
            })
          }

          function prefillRowAcquisition(selectElement) {
            const itemRow = selectElement.closest('tr');

            const description = itemRow.querySelector('input[name="item_description[]"]');
            const currentQuantity = itemRow.querySelector('input[name="current_quantity[]"]');
            const remainingQty = itemRow.querySelector('input[name="item_qty_remaining[]"]');
            const qty = itemRow.querySelector('input[name="item_qty[]"]');

            const itemName = itemRow.querySelector('input[name="item_name[]"]');
            const itemSerial = itemRow.querySelector('input[name="item_serial[]"]');

            const warehouseID = itemRow.querySelector('input[name="warehouse_id[]"]');
            const binID = itemRow.querySelector('input[name="bin_id[]"]');

            const selectedItem = selectElement.selectedOptions[0];

            itemName.value = selectedItem.getAttribute('attr-data-name');
            itemSerial.value = selectedItem.getAttribute('attr-data-serial');
            description.value = selectedItem.getAttribute('attr-data-description');
            currentQuantity.value = selectedItem.getAttribute('attr-current-quantity');

            warehouseID.value = selectedItem.getAttribute('attr-current-warehouse-id');
            binID.value = selectedItem.getAttribute('attr-current-bin-id');

            remainingQty.value = parseInt(currentQuantity.value) - 1;
            qty.value = 1;
            qty.max = currentQuantity.value;
            qty.setAttribute('attr-min-threshold', selectedItem.getAttribute('attr-min-threshold'));

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
   
  'tableAction' => true,
  'bootstrapselect' => true,
  ]
  )
  @include('universal-layout.alert')
