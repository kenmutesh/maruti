@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Add Purchase Order',
'select2bs4' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
  .bootstrap-select {
    padding: 0 !important;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/purchases'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">

            <form class="" onsubmit="showSpinner(event)" action="{{ route('purchaseorders.store') }}" method="post" autocomplete="off" enctype="multipart/form-data">
              @csrf
              <div class="row">

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <div class="form-group">
                    <label>
                      <input type="checkbox" name="without_po"/>Use without PO Number
                    </label>
                    <label for="poNumber">PO Number</label>
                    <input type="hidden" name="lpo_prefix" value="{{ $purchaseorder->next_purchase_order_prefix }}">
                    <input type="hidden" name="lpo_suffix" value="{{ $purchaseorder->next_purchase_order_suffix }}">
                    <input type="text" required name="po_number" class="form-control dark-text" id="poNumber" aria-describedby="poNumber" readonly value="{{ $purchaseorder->next_purchase_order_prefix }}{{ $purchaseorder->next_purchase_order_suffix }}">
                  </div>
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select class="form-control searchable-select supplier-select ms search-select" required name="supplier_id" data-live-search="true">
                      @foreach($suppliers as $singleSupplier)
                      <option value="{{ $singleSupplier->id }}">
                        {{ $singleSupplier->supplier_name }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Record Date</label>
                  <input type="date" required name="record_date" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Due Date</label>
                  <input type="date" name="due_date" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Quotation Referrence Number</label>
                  <input type="text" name="quotation_ref" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Quotation Documents</label>
                  <input type="file" multiple name="quotation_docs[]" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Memo</label>
                  <input type="text" name="memo_ref" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Memo Documents</label>
                  <input type="file" multiple name="memo_docs[]" class="form-control dark-text">
                </div>

              </div>

              <div class="row">

                <div class="col-sm-6">
                  <label>Currency</label>
                  <select class="searchable-select form-control ms" name="currency">
                    <option value="KES">Kenyan Shillings(Ksh)</option>
                    <option value="$">US Dollar($)</option>
                  </select>
                </div>

                <div class="col-sm-6">
                  <label>Grand Total</label>
                  <input type="text" name="grand_total" readonly class="form-control" value="">
                </div>

                <div class="col-sm-6">
                  <label>Total Discount</label>
                  <input type="text" name="grand_total_discount" class="form-control" value="">
                </div>

              </div>

              <div class="table-responsive mt-3 w-80" style="overflow-x: auto;display:block;overflow:initial;">
                <table class="table table-bordered col w-70 mx-auto">
                  <th class="p-0 border text-center">Item</th>
                  <th class="p-0 border text-center">Color/Name</th>
                  <th class="p-0 border text-center">Qty</th>
                  <th class="p-0 border text-center">Powder(KG)</th>
                  <th class="p-0 border text-center">Unit Cost</th>
                  <th class="p-0 border text-center">VAT(%)</th>
                  <th class="p-0 border text-center">Unit Cost(+VAT)</th>
                  <th class="p-0 border text-center">Total Amt</th>
                  <th class="p-0 border text-center">Action</th>
                  <tbody class="item-list">
                    <tr>
                      <td class="p-0 position-relative" style="z-index: 5;">
                        <input type="hidden" name="item_type[]" value="">
                        <select style="width:200px;" class="searchable-select ms search-select" name="item_id[]" onchange="prefillPurchaseOrderRow(this)" data-live-search="true" data-style="text-white">
                          <option disabled selected>Choose item</option>
                          <optgroup label="Powder" data-type="POWDER">
                            <option value="New Powder" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New Powder
                            </option>
                            @forelse($powders as $powder)
                            <option value="{{ $powder->id }}" attr-data-cost="{{ $powder->standard_cost }}" attr-data-cost-vat="{{ $powder->standard_cost_vat }}" attr-data-cost-without-vat="{{ $powder->standard_cost_without_vat }}" attr-data-name="{{ $powder->powder_color }}">
                              {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }})
                            </option>
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>

                          @forelse($inventoryitems as $type => $items)
                          <optgroup label="{{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}">
                            <?php $items = json_decode(json_encode($items)) ?>
                            <option value="New {{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New {{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}</option>
                            @forelse($items as $item)
                            <option value="{{ $item->id }}" attr-data-cost="{{ $item->standard_cost }}" attr-data-cost-vat="{{ $item->standard_cost_vat }}" attr-data-cost-without-vat="{{ $item->standard_cost_without_vat }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>
                          @empty
                          No registered inventory items
                          @endforelse

                          <optgroup label="Non Inventory" data-type="NON INVENTORY">
                            <option value="New Non Invenotry" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New Non Inventory
                            </option>
                            @forelse($noninventoryitems as $item)
                            <option value="{{ $item->id }}" attr-data-cost="{{ $item->standard_cost }}" attr-data-cost-vat="{{ $item->standard_cost_vat }}" attr-data-cost-without-vat="{{ $item->standard_cost_without_vat }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>

                        </select>
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="text" name="item_name[]" value="">
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step="1" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_qty[]" value="">
                      </td>

                      <td class="p-0 overflow-hidden">
                        <input class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_kg[]">
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="unit_cost_without_vat[]">
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step=".01" max="100" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_vat[]">
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="unit_cost_with_vat[]">
                      </td>

                      <td class="p-0">
                        <div class="d-flex flex-column h-100">
                          <input class="w-100" type="number" step=".01" readonly name="amount[]">
                        </div>
                      </td>

                      <td class="p-0">
                        <button type="button" name="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">REMOVE</button>
                        <button type="button" name="button" class="btn btn-sm btn-info" onclick="resetRow(this)">RESET</button>
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
                  <label>Terms and conditions</label>
                  <textarea name="terms" class="form-control p-3" required style="border: 1px solid #2b3553;border-radius: .25rem;" rows="5" placeholder="Terms and conditions"></textarea>
                </div>
              </div>

              <button type="submit" name="submit_btn" value="Create Purchase Order" class="btn btn-success text-white w-100">CREATE PURCHASE ORDER</button>


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

          @include('universal-layout.scripts',
          [
          'vendorscripts' => true,
          'mainscripts' => true,
          'jquery' => true,
          'select2' => true,
          'tableAction' => true,
          'onTheFlyApi' => true,
          ]
          )
          @include('universal-layout.footer')