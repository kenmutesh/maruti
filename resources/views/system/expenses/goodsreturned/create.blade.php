@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Add Goods Returned Note',
'bootstrapselect' => true,
]
)
<style>
  .modal-backdrop.show {
    z-index: 4;
  }
  .table-fixed-2 td,
  .table-fixed-2 th {
    width: 1rem;
    overflow: hidden;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/expenses'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

      <div class="content">

        <form class="" onsubmit="showSpinner(event)" action="{{ route('create_goodsreturned_note') }}" method="post" autocomplete="off" enctype="multipart/form-data">
          @csrf
          <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label for="supplier">Supplier</label>
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#createSupplierForm">
                  Add Supplier
                </button>
                <select class="form-control searchable-select supplier-select" required name="supplier_id">
                  @foreach($suppliers as $singleSupplier)
                    <option value="{{ $singleSupplier->id }}">
                      {{ $singleSupplier->supplier_name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <label>Record Date</label>
              <input type="date" required name="record_date" class="form-control dark-text">
            </div>

            <div class="col-sm-3">
              <label>Invoice Number</label>
              <input type="text" required name="invoice_ref" class="form-control dark-text">
            </div>

            <div class="col-sm-3">
              <label>Invoice Documents</label>
              <input type="file" multiple name="invoice_docs[]" class="form-control dark-text">
            </div>

          </div>

          <div class="row">

            <div class="col-sm-3">
              <label>Credit Note Number</label>
              <input type="text" required name="credit_note_ref" class="form-control dark-text">
            </div>

            <div class="col-sm-3">
              <label>Credit Note Documents</label>
              <input type="file" multiple name="credit_note_docs[]" class="form-control dark-text">
            </div>


            <div class="col-sm-3">
              <label>Grand Total</label>
              <input type="text" name="grand_total" readonly class="form-control" value="">
            </div>

          </div>

          <div class="table-responsive mt-3 w-80" style="overflow-x: auto;display:block;overflow:initial;">
            <table class="table table-bordered col w-70 mx-auto">
              <th class="p-0 border">Item</th>
              <th class="p-0 border">Color/Name</th>
              <th class="p-0 border">Code</th>
              <th class="p-0 border">Description</th>
              <th class="p-0 border">Quantity</th>
              <th class="p-0 border">KG</th>
              <th class="p-0 border">Tax(%)</th>
              <th class="p-0 border">Unit Cost</th>
              <th class="p-0 border">Total Amt</th>
              <th class="p-0 border">Action</th>
              <tbody class="item-list">
                <tr>
                  <td>
                    <input type="hidden" name="item_id[]" value="">
                    <input type="hidden" name="inventory_type[]" value="">
                    <input type="hidden" name="warehouse_id[]" value="">
                    <input type="hidden" name="bin_id[]" value="">

                    <select style="width:200px;" class="searchable-select" onchange="prefillItemRow(this, true)" data-live-search="true" data-style="text-white" >
                      <option disabled selected>Choose from inventory</option>
                      <optgroup label="POWDER">
                        <?php
                          foreach ($powderInventory as $powderItem) {
                            if ($powderItem->total_weight < 1) {
                              continue;
                            }
                            ?>
                              <option
                                value="<?php echo $powderItem->item_id ?>"
                                attr-data-description="<?php echo $powderItem->powder_description ?>"
                                attr-data-code="<?php echo $powderItem->powder_code ?>"
                                attr-data-cost="<?php echo $powderItem->standard_cost ?>"
                                attr-data-cost="<?php echo $powderItem->standard_cost ?>"
                                attr-warehouse-id="<?php echo $powderItem->warehouse_id ?>"
                                attr-bin-id="<?php echo $powderItem->bin_id ?>"
                                attr-data-name ="<?php echo $powderItem->powder_color ?>"
                                attr-current-quantity ="<?php echo $powderItem->current_weight ?>">
                                  <?php echo $powderItem->powder_color ?>(<?php echo $powderItem->warehouse_name ?>)
                              </option>
                            <?php
                          }
                        ?>
                      </optgroup>

                      <optgroup label="HARDWARE">
                        <?php
                          foreach ($hardwareInventory as $hardwareItem) {
                            // don't list quantity below one
                            if ($hardwareItem->current_quantity < 1) {
                              continue;
                            }
                              ?>
                                <option
                                  value="<?php echo $hardwareItem->item_id ?>"
                                  attr-data-description="<?php echo $hardwareItem->item_description ?>"
                                  attr-data-code="<?php echo $hardwareItem->item_code ?>"
                                  attr-data-cost="<?php echo $hardwareItem->standard_cost ?>"
                                  attr-warehouse-id="<?php echo $hardwareItem->warehouse_id ?>"
                                  attr-bin-id="<?php echo $hardwareItem->bin_id ?>"
                                  attr-data-name ="<?php echo $hardwareItem->item_name ?>"
                                  attr-current-quantity ="<?php echo $hardwareItem->current_quantity ?>">
                                    <?php echo $hardwareItem->item_name ?>(<?php echo $hardwareItem->warehouse_name ?>)
                                </option>
                              <?php
                          }
                        ?>
                      </optgroup>

                      <optgroup label="ALUMINIUM">
                        <?php
                          foreach ($aluminiumInventory as $aluminiumItem) {
                            // don't list quantity below one
                            if ($aluminiumItem->current_quantity < 1) {
                              continue;
                            }
                              ?>
                                <option
                                  value="<?php echo $aluminiumItem->item_id ?>"
                                  attr-data-description="<?php echo $aluminiumItem->item_description ?>"
                                  attr-data-code="<?php echo $aluminiumItem->item_code ?>"
                                  attr-data-cost="<?php echo $aluminiumItem->standard_cost ?>"
                                  attr-warehouse-id=<?php echo $aluminiumItem->warehouse_id ?>
                                  attr-bin-id="<?php echo $aluminiumItem->bin_id ?>"
                                  attr-data-name ="<?php echo $aluminiumItem->item_name ?>"
                                  attr-current-quantity ="<?php echo $aluminiumItem->current_quantity ?>">
                                  <?php echo $aluminiumItem->item_name ?>(<?php echo $aluminiumItem->warehouse_name ?>)
                                </option>
                              <?php
                          }
                        ?>
                      </optgroup>

                    </select>
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="text" readonly name="item_name[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="text" readonly name="item_code[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="text" readonly name="item_description[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="number" onchange="calculateItemRowTotal(this)" name="item_qty[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="number" onchange="calculateItemRowTotal(this)" name="item_kg[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="text" onkeyup="calculateItemRowTotal(this)" readonly name="item_tax[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="text" onkeyup="calculateItemRowTotal(this)" name="unit_cost[]" value="">
                  </td>

                  <td class="p-0">
                    <input class="w-100" type="text" name="amount[]" readonly>
                  </td>

                  <td>
                    <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
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

          <div class="row">
            <div class="col">
              <label>Memo</label>
              <textarea name="memo" class="form-control p-3" required style="border: 1px solid #2b3553;border-radius: .25rem;" rows="5" placeholder="Memo"></textarea>
            </div>
          </div>

          <button type="submit" name="submit_btn" value="Create Purchase Order" class="btn btn-success text-white">CREATE GOODS RETURNED NOTE</button>


        </form>

        <!-- modal for creating supplier on the fly -->
        <div class="modal fade" id="createSupplierForm" tabindex="-1" role="dialog" aria-labelledby="createSupplierForm" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create A Supplier in the System</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="tim-icons icon-simple-remove"></i>
                </button>
              </div>
              <div class="modal-body">

                <form method="POST" autocomplete="off" action="{{ route('suppliers.store') }}">
                  @csrf
                  <div class="d-flex flex-column">

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="supplierName">Supplier Name</label>
                          <input type="text" required name="supplier_name" class="form-control dark-text" id="supplierName" aria-describedby="supplierName" placeholder="Enter supplier name">
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="supplierEmail">Supplier Email</label>
                          <input type="text" required name="supplier_email" class="form-control dark-text" id="supplierEmail" aria-describedby="supplierEmail" placeholder="Enter supplier email">
                        </div>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="supplierMobile">Supplier Mobile</label>
                        <input type="text" required name="supplier_mobile" class="form-control" id="supplierMobile" aria-describedby="supplierMobile" class="dark-text" placeholder="Enter supplier mobile">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="supplierDescription">Supplier Description</label>
                        <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="supplier_description" class="form-control" rows="3" required id="supplierDescription"></textarea>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="companyLocation">Company Location</label>
                        <input type="text" required name="company_location" class="form-control" id="companyLocation" aria-describedby="companyLocation" class="dark-text" placeholder="Enter company location">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="companyPIN">Company PIN Number</label>
                        <input type="text" required name="company_pin" class="form-control" id="companyPIN" aria-describedby="companyPIN" class="dark-text" placeholder="Enter company location">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="companyBox">Company PO Box</label>
                        <input type="text" required name="company_box" class="form-control" id="companyBox" aria-describedby="companyBox" class="dark-text" placeholder="Enter company box">
                      </div>
                    </div>

                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                @csrf
                <button type="button" onclick="addSupplierViaAPI(this)" name="submit_btn" value="Create Supplier" class="btn btn-primary">CREATE SUPPLIER</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
        </div>
      </div>
    </div>
  </section>

      <script type="text/javascript">
      const supplierDropdown = document.querySelector('.supplier-select');
      let success = true;
      async function addSupplierViaAPI(submitBtnElement, tokenInput) {
          // reference to the previous html content
          const previousHTML = submitBtnElement.innerHTML;
          submitBtnElement.disabled = true;
          // show the spinner
          submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

          const formElement = submitBtnElement.form;

          let formData = new FormData();
          formData.append("supplier_name", formElement.supplier_name.value);
          formData.append("supplier_email", formElement.supplier_email.value);
          formData.append("supplier_mobile", formElement.supplier_mobile.value);
          formData.append("supplier_description", formElement.supplier_description.value);
          formData.append("company_location", formElement.company_location.value);
          formData.append("company_pin", formElement.company_pin.value);
          formData.append("company_box", formElement.company_box.value);
          formData.append("_token", "{{ csrf_token() }}");

          try {
            let response = await fetch('/suppliers/storeAPI', {
              method: 'POST',
              body: formData
            });

            let addSupplierResponse = await response.json();
            const option = document.createElement('option');
            option.value = addSupplierResponse.data.id;
            option.innerHTML = addSupplierResponse.data.supplier_name;
            option.selected = true;
            supplierDropdown.append(option);

          } catch (e) {

            console.log(e);

            success = false;

          }

          // a delay to show responses
          setTimeout(()=>{
            if (success) {

              $.notify({
                      icon: "tim-icons ui-1_bell-53",
                      message: 'Supplier has been added successfully'
                    }, {
                        type: 'success'
                      });

              formElement.reset();

            }else {
              $.notify({
                      icon: "tim-icons ui-1_bell-53",
                      message: 'Failed to add supplier please retry'
                    }, {
                        type: 'error'
                      });
            }
          }, 800)


          setTimeout(()=>{
            submitBtnElement.innerHTML = previousHTML;
            submitBtnElement.disabled = false;
          }, 2000)


        }
      </script>

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
