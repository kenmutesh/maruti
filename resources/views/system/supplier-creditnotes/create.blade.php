@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Create Credit Note',
'select2bs4' => true,
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
  'slug' => '/purchases'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

          <div class="content">

            <form class="" onsubmit="showSpinner(event)" action="{{ route('suppliercreditnotes.store') }}" method="post" autocomplete="off" enctype="multipart/form-data">
              @csrf
              <div class="row">

                <div class="col">
                  <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select class="form-control searchable-select supplier-select ms search-select" required name="supplier_id" data-live-search="true" onchange="updatePurchases(this)">
                      <option disabled selected>Choose supplier</option>
                      @foreach($suppliers as $singleSupplier)
                        <option value="{{ $singleSupplier->id }}">
                          {{ $singleSupplier->supplier_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col">
                  <div class="form-group">
                    <label for="supplier">Purchase Order</label>
                    <select name="purchase_order_id" class="form-control ms search-select purchase-select">

                    </select>
                  </div>
                </div>

                <div class="col">
                  <div class="form-group">
                    <label for="poNumber">Credit Note Number</label>
                    <div class="row">
                      <input class="border-0 col-6 p-0 text-right" type="text" readonly name="credit_prefix" value="{{ $creditNote->next_credit_note_prefix }}">
                      <input class="border-0 col-6 p-0" type="text" readonly name="credit_suffix" value="{{ $creditNote->next_credit_note_suffix }}">
                    </div>
                  </div>
                </div>

              </div>

              <div class="row">

                <div class="col-sm-6">
                  <label>Date</label>
                  <input type="date" required name="date" class="form-control dark-text">
                </div>

                <div class="col-sm-6">
                  <label>Grand Total</label>
                  <input type="text" name="grand_total" readonly class="form-control" value="">
                </div>

              </div>
              <div class="form-check form-check-radio form-check-inline d-none">
                <label class="form-check-label">
                  <input class="form-check-input" type="radio" checked name="belongs_to" id="maruti" onchange="toggleActiveItemList(this)" value="2" data-value="DIRECT-SALE"> DIRECT-SALE
                  <span class="form-check-sign"></span>
                </label>
              </div>

              <div class="table-responsive mt-3 w-80" style="overflow-x: auto;display:block;overflow:initial;">
                <table class="table table-bordered col w-100 mx-auto">
                  <thead>
                    <tr>
                      <th class="p-0 border text-center">Item</th>
                      <th class="p-0 border text-center">Quantity</th>
                      <th class="p-0 border text-center">KG</th>
                      <th class="p-0 border text-center">Boxes</th>
                      <th class="px-2 border text-center">UoM</th>
                      <th class="p-0 border text-center">Unit Price</th>
                      <th class="px-2 border text-center">Vat(%)</th>
                      <th class="p-0 border text-center">Vat Inclusive</th>
                      <th class="p-0 border text-center">Total Amt</th>
                      <th class="p-0 border text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody class="item-list maruti-direct">

                    <tr>
                      <td>
                        <input type="hidden" name="maruti_direct_inventory_type[]">
                        <select style="width:200px;" name="maruti_direct_item_id[]" class="form-control search-select ms" onchange="prefillItemRowMarutiDirect(this)" data-style="text-white" data-live-search="true">
                          <option disabled selected>Choose from inventory</option>

                          @forelse($inventoryitems as $type => $items)
                          <optgroup label="{{ App\Enums\InventoryItemsEnum::from($type)->humanreadablestring() }}">
                            <?php $items = json_decode(json_encode($items)) ?>
                            @forelse($items as $item)
                            <option value="{{ $item->id }}" attr-data-price="{{ $item->standard_price }}" attr-data-current-quantity="{{ $item->current_quantity }}" attr-data-price-vat="{{ $item->standard_price_vat }}" attr-data-price-without-vat="{{ $item->standard_price_without_vat }}" attr-data-uom="{{ $item->quantity_tag }}" attr-data-name="{{ $item->item_name }}">
                              {{ $item->item_name }}
                            </option>
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>
                          @empty
                          No registered inventory items
                          @endforelse
                          <optgroup label="Powder" data-type="POWDER">
                            @forelse($powders as $powder)
                            <option value="{{ $powder->id }}" attr-data-price="{{ $powder->standard_price }}" attr-data-current-quantity="{{ $powder->current_weight }}" attr-data-price-vat="{{ $powder->standard_price_vat }}" attr-data-price-without-vat="{{ $powder->standard_price_without_vat }}" attr-data-uom="KG" attr-data-name="{{ $powder->powder_color }}">
                              {{ $powder->powder_color }} - ({{ $powder->supplier->supplier_name }})
                            </option>
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>
                          <optgroup label="NON-INVENTORY" data-type="NON-INVENTORY">
                            @forelse($noninventoryitems as $noninventoryitem)
                            <option value="{{ $noninventoryitem->id }}" attr-data-price="{{ $noninventoryitem->standard_price }}" attr-data-price-vat="{{ $noninventoryitem->standard_price_vat }}" attr-data-price-without-vat="{{ $noninventoryitem->standard_price_without_vat }}" attr-data-uom="UNIT" attr-data-name="{{ $noninventoryitem->item_name }}">
                              {{ $noninventoryitem->item_name }}
                            </option>
                            @empty
                            <option>No item under category</option>
                            @endforelse
                          </optgroup>
                        </select>
                      </td>

                      <td>
                        <input type="number" min="1" step="any" style="width:4rem;" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_item_qty[]" value="">
                      </td>

                      <td>
                        <input type="number" step="any" style="width:4rem;" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_item_kg[]" value="">
                      </td>

                      <td>
                        <input type="text" style="width:4rem;" name="maruti_direct_item_boxes[]" value="">
                      </td>

                      <td>
                        <input type="text" style="width:4rem;" name="maruti_direct_uom[]" value="">
                      </td>

                      <td>
                        <input type="number" step="any" onkeyup="calculateMarutiDirectItemRowTotal(this)" name="maruti_direct_unit_price[]" class="w-100" value="">
                      </td>

                      <td>
                        <input type="number" step="any" max="100" name="maruti_direct_unit_vat[]" class="w-100" value="">
                      </td>

                      <td>
                        <select class="w-100 ms" name="maruti_direct_vat_inclusive[]">
                          <option value="Yes">Yes</option>
                          <option value="No">No</option>
                        </select>
                      </td>

                      <td>
                        <input type="text" class="maruti-total w-100" name="maruti_direct_amount[]" readonly>
                      </td>

                      <td>
                        <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                      </td>

                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="100%">
                        <button type="button" name="button" onclick="addItemRow('.maruti-direct')" class="btn btn-danger">ADD
                          ITEM</button>
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
              <div class="d-flex justify-content-around">
                <button type="submit" name="submit_btn" value="Create Coating Job" class="btn btn-success text-white w-100 rounded-pill my-4">CREATE</button>
              </div>


            </form>


          </div>

          <script type="text/javascript">
            async function updatePurchases(selectElement) {
              const purchaseList = document.querySelector('.purchase-select');
              purchaseList.innerHTML = '';
              $(purchaseList).select2('destroy');
              const supplierID = selectElement.value;

              const request = await fetch(`/suppliers/creditnotes/purchaseorders/${supplierID}`);
              if (request.ok) {
                purchaseList.innerHTML = await request.text();
              } else {
                purchaseList.innerHTML = "<option disabled>Failed in getting data</option>";
              }
              $(purchaseList).select2();
            }

            const customerDropdown = document.querySelector('.customer-select');
            let success = true;

            function triggerPrefill(selectElement) {
              console.log('helo');
              const event = new Event('change');
              selectElement.dispatchEvent(event);
            }

            window.addEventListener('load', () => {
              const selectElements = document.querySelectorAll('.inventory-select');
              [...selectElements].forEach((selectElement) => {
                triggerPrefill(selectElement);
              })
            })
          </script>

          @include('universal-layout.scripts',[
          'libscripts' => true,
          'vendorscripts' => true,
          'mainscripts' => true,
          'select2bs4' => true,
          'coating' => true
          ])
          @include('universal-layout.alert')
          @include('universal-layout.footer')