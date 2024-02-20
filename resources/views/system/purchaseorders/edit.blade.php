@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Edit Purchase Order',
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

            <form class="" onsubmit="showSpinner(event)" action="{{ route('purchaseorders.update', $purchaseorder->id) }}" method="post" autocomplete="off" enctype="multipart/form-data">
              @csrf
              @method("PUT")
              <div class="row">

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <div class="form-group">
                    <label for="poNumber">PO Number</label>
                    <input type="hidden" name="lpo_prefix" value="{{ $purchaseorder->lpo_prefix }}">
                    <input type="hidden" name="lpo_suffix" value="{{ $purchaseorder->lpo_suffix }}">
                    <input type="text" required name="po_number" class="form-control dark-text" id="poNumber" aria-describedby="poNumber" readonly value="{{ $purchaseorder->lpo_prefix }}{{ $purchaseorder->lpo_suffix }}">
                  </div>
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select class="form-control searchable-select supplier-select ms search-select" required name="supplier_id" data-live-search="true">
                      @foreach($suppliers as $supplier)
                      @if($supplier->id == $purchaseorder->supplier_id)
                      <option value="{{ $supplier->id }}" selected>
                        {{ $supplier->supplier_name }} (CURRENT)
                      </option>
                      @else
                      <option value="{{ $supplier->id }}">
                        {{ $supplier->supplier_name }}
                      </option>
                      @endif

                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Record Date</label>
                  <input type="date" required value="{{ $purchaseorder->record_date }}" name="record_date" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Due Date</label>
                  <input type="date" name="due_date" value="{{ $purchaseorder->due_date }}" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Quotation Referrence Number</label>
                  <input type="text" name="quotation_ref" value="{{ $purchaseorder->quotation_ref }}" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Quotation Documents</label>
                  <input type="file" multiple name="quotation_docs[]" class="form-control dark-text">
                  <button type="button" class="btn btn-sm btn-primary m-0" data-toggle="modal" data-target="#quotationDocs">
                    View Documents
                  </button>

                  <div class="modal fade" id="quotationDocs">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">Documents</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          <p>Click checkbox for deletion</p>
                          @foreach($purchaseorder->purchaseorderdocuments as $purchaseorderdocument)
                            @if($purchaseorderdocument->type != App\Enums\PurchaseOrderDocumentsEnum::QUOTATION->value)
                              @continue
                            @endif
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <input type="checkbox" name="document[]" value="{{ $purchaseorderdocument->id }}">
                              </div>
                            </div>
                            <input type="text" readonly class="form-control" value="{{ $purchaseorderdocument->document_name }}">
                          </div>
                          <a class="w-100 btn btn-primary" target="_blank" href="{{ asset('/storage/'. $purchaseorderdocument->document_path ) }}">View</a>
                          @endforeach
                          
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Memo</label>
                  <input type="text" name="memo_ref" value="{{ $purchaseorder->memo_ref }}" class="form-control dark-text">
                </div>

                <div class="col-sm-3 d-flex flex-column justify-content-center mb-2">
                  <label>Memo Documents</label>
                  <input type="file" multiple name="memo_docs[]" class="form-control dark-text">
                  <button type="button" class="btn btn-sm btn-primary m-0" data-toggle="modal" data-target="#memoDocs">
                    View Current
                  </button>

                  <div class="modal fade" id="memoDocs">
                  <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">Documents</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          <p>Click checkbox for deletion</p>
                          @foreach($purchaseorder->purchaseorderdocuments as $purchaseorderdocument)
                            @if($purchaseorderdocument->type != App\Enums\PurchaseOrderDocumentsEnum::MEMO->value)
                              @continue
                            @endif
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <input type="checkbox" name="document[]" value="{{ $purchaseorderdocument->id }}">
                              </div>
                            </div>
                            <input type="text" readonly class="form-control" value="{{ $purchaseorderdocument->documnent_name }}">
                          </div>
                          @endforeach
                          
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>

              <div class="row">

                <div class="col-sm-6">
                  <label>Currency</label>
                  <select class="searchable-select form-control ms" name="currency">
                    @if($purchaseorder->currency == "KES")
                    <option value="KES" selected>Kenyan Shillings(Ksh) (CURRENT)</option>
                    @else
                    <option value="KES">Kenyan Shillings(Ksh)</option>
                    @endif
                    @if($purchaseorder->currency == "$")
                    <option value="$" selected>US Dollar($) (CURRENT)</option>
                    @else
                    <option value="$">US Dollar($)</option>
                    @endif
                  </select>
                </div>

                <div class="col-sm-6">
                  <label>Grand Total</label>
                  <input type="text" name="grand_total" readonly class="form-control" value="{{ $purchaseorder->grand_total }}">
                </div>

                <div class="col-sm-6">
                  <label>Total Discount</label>
                  <input type="text" name="grand_total_discount" class="form-control" value="{{ $purchaseorder->discount }}">
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

                    @foreach($purchaseorder->purchaseorderitems as $purchaseorderitem)
                    <tr>
                      <td class="p-0 position-relative" style="z-index: 5;">
                        <input type="hidden" name="purchase_order_item_id[]" value="{{ $purchaseorderitem->id }}">
                        <input type="hidden" name="item_type[]" value="{{ $purchaseorderitem->item_type }}">
                        <select style="width:200px;" class="searchable-select ms search-select" name="item_id[]" onchange="prefillPurchaseOrderRow(this)" data-live-search="true" data-style="text-white">
                          <option disabled selected>Choose item</option>

                          <optgroup label="Powder" data-type="POWDER">
                            @if($purchaseorderitem->item_type == "POWDER" && $purchaseorderitem->new_item_name)
                            <option selected value="New Powder" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New Powder
                            </option>
                            @else
                            <option value="New Powder" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New Powder
                            </option>
                            @endif
                            @forelse($powders as $powder)
                              @if($purchaseorderitem->powder_id == $powder->id)
                              <option selected value="{{ $powder->id }}" attr-data-cost="{{ $powder->standard_cost }}" attr-data-cost-vat="{{ $powder->standard_cost_vat }}" attr-data-cost-without-vat="{{ $powder->standard_cost_without_vat }}" attr-data-name="{{ $powder->powder_color }}">
                                {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }}) (CURRENT)
                              </option>
                              @else
                              <option value="{{ $powder->id }}" attr-data-cost="{{ $powder->standard_cost }}" attr-data-cost-vat="{{ $powder->standard_cost_vat }}" attr-data-cost-without-vat="{{ $powder->standard_cost_without_vat }}" attr-data-name="{{ $powder->powder_color }}">
                                {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }})
                              </option>
                              @endif
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>

                          @forelse($inventoryitems as $type => $items)
                          <optgroup label="{{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}">
                            <?php $items = json_decode(json_encode($items)) ?>
                            @if($purchaseorderitem->item_type == strtoupper(App\Enums\InventoryItemsEnum::from($type)->humanreadablestring()) && $purchaseorderitem->new_item_name)
                            <option selected value="New {{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New {{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}
                            </option>
                            @else
                            <option value="New {{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New {{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}
                            </option>
                            @endif
                            @forelse($items as $item)
                            @if($purchaseorderitem->inventory_item_id == $item->id)
                            <option selected value="{{ $item->id }}" attr-data-cost="{{ $item->standard_cost }}" attr-data-cost-vat="{{ $item->standard_cost_vat }}" attr-data-cost-without-vat="{{ $item->standard_cost_without_vat }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @else
                            <option value="{{ $item->id }}" attr-data-cost="{{ $item->standard_cost }}" attr-data-cost-vat="{{ $item->standard_cost_vat }}" attr-data-cost-without-vat="{{ $item->standard_cost_without_vat }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @endif
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>
                          @empty
                          No registered inventory items
                          @endforelse

                          <optgroup label="Non Inventory" data-type="NON INVENTORY">
                            @if($purchaseorderitem->item_type == "NON INVENTORY" && $purchaseorderitem->new_item_name)
                            <option selected value="New Non Invenotry" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New Non Inventory
                            </option>
                            @else
                            <option value="New Non Invenotry" attr-data-cost="0" attr-data-cost-vat="0" attr-data-cost-without-vat="0" attr-data-name="">
                              New Non Inventory
                            </option>
                            @endif
                            @forelse($noninventoryitems as $item)
                            @if($purchaseorderitem->non_inventory_item_id == $item->id)
                            <option selected value="{{ $item->id }}" attr-data-cost="{{ $item->standard_cost }}" attr-data-cost-vat="{{ $item->standard_cost_vat }}" attr-data-cost-without-vat="{{ $item->standard_cost_without_vat }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @else
                            <option value="{{ $item->id }}" attr-data-cost="{{ $item->standard_cost }}" attr-data-cost-vat="{{ $item->standard_cost_vat }}" attr-data-cost-without-vat="{{ $item->standard_cost_without_vat }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @endif
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>

                        </select>
                      </td>

                      <td class="p-0">
                        @if($purchaseorderitem->new_item_name)
                        <input class="w-100" type="text" name="item_name[]" value="{{ $purchaseorderitem->new_item_name }}">
                        @elseif($purchaseorderitem->powder_id)
                        <input class="w-100" type="text" name="item_name[]" value="{{ $purchaseorderitem->powder->powder_color }}">
                        @elseif($purchaseorderitem->inventory_item_id)
                        <input class="w-100" type="text" name="item_name[]" value="{{ $purchaseorderitem->inventoryitem->item_name }}">
                        @else
                        <input class="w-100" type="text" name="item_name[]" value="{{ $purchaseorderitem->noninventoryitem->item_name }}">
                        @endif
                      </td>

                      <td class="p-0">
                        @if($purchaseorderitem->item_type == "POWDER")
                        <input readonly class="w-100" type="number" step="1" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_qty[]" value="0">
                        @else
                        <input class="w-100" type="number" step="1" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_qty[]" value="{{ $purchaseorderitem->quantity }}">
                        @endif
                      </td>

                      <td class="p-0 overflow-hidden">
                        @if($purchaseorderitem->item_type == "POWDER")
                        <input class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_kg[]" value="{{ $purchaseorderitem->quantity }}">
                        @else
                        <input readonly class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_kg[]" value="0">
                        @endif
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="unit_cost_without_vat[]" value="{{ $purchaseorderitem->cost }}">
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step=".01" max="100" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="item_vat[]" value="{{ $purchaseorderitem->vat }}">
                      </td>

                      <td class="p-0">
                        <input class="w-100" type="number" step=".01" onkeyup="calculatePurchaseOrderItemRowTotal(this)" name="unit_cost_with_vat[]" value="{{ ($purchaseorderitem->cost + $purchaseorderitem->vat_addition) }}">
                      </td>

                      <td class="p-0">
                        <div class="d-flex flex-column h-100">
                          <input class="w-100" type="number" step=".01" readonly name="amount[]" value="{{ $purchaseorderitem->sub_total + ($purchaseorderitem->vat_addition * $purchaseorderitem->quantity) }}">
                        </div>
                      </td>

                      <td class="p-0">
                        <button type="button" name="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">REMOVE</button>
                        <button type="button" name="button" class="btn btn-sm btn-info" onclick="resetRow(this)">RESET</button>
                      </td>

                    </tr>
                    @endforeach
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
                  <textarea name="terms" class="form-control p-3" required style="border: 1px solid #2b3553;border-radius: .25rem;" rows="5" placeholder="Terms and conditions">{{ $purchaseorder->terms }}</textarea>
                </div>
              </div>

              <button type="submit" name="submit_btn" value="Edit Purchase Order" class="btn btn-success text-white w-100">EDIT PURCHASE ORDER</button>


            </form>


          </div>

          @include('universal-layout.scripts',
          [
          'libscripts' => true,
          'vendorscripts' => true,
          'mainscripts' => true,
          'jquery' => true,
          'select2' => true,
          'tableAction' => true,
          'onTheFlyApi' => true,
          ]
          )
          @include('universal-layout.footer')