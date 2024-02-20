@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Complete Purchase Order',
'select2' => true,
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

            <form class="" onsubmit="showSpinner(event)" action="{{ route('purchaseorders.complete', $purchaseorder->id) }}" method="post" autocomplete="off" enctype="multipart/form-data">
              @csrf
              @method("PUT")
              <input type="hidden" name="po_id" value="{{ $purchaseorder->id }}">
              <div class="row">

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="poNumber">PO Number</label>
                    <input type="hidden" name="lpo_prefix" value="{{ $purchaseorder->lpo_prefix }}">

                    <input type="hidden" name="lpo_suffix" value="{{ $purchaseorder->lpo_suffix }}">

                    <input type="text" required name="po_number" class="form-control dark-text" id="poNumber" aria-describedby="poNumber" placeholder="Enter PO number" readonly value="{{ $purchaseorder->lpo_prefix }}{{ $purchaseorder->lpo_suffix }}">
                  </div>
                </div>

                <div class="col-sm-6">
                  <label>Grand Total</label>
                  <input type="text" readonly name="grand_total" class="form-control" value="{{ $purchaseorder->grand_total }}">
                </div>

              </div>

              <div class="row">

                <div class="col-sm-6">
                  <label>Invoice Number</label>
                  <input type="text" name="invoice_ref" class="form-control dark-text">
                </div>

                <div class="col-sm-6">
                  <label>Invoice Documents</label>
                  <input type="file" multiple name="invoice_docs[]" class="form-control dark-text">
                </div>

              </div>

              <div class="row">

                <div class="col-sm-6">
                  <label>Delivery Note Number</label>
                  <input type="text" name="delivery_ref" class="form-control dark-text">
                </div>

                <div class="col-sm-6">
                  <label>Delivery Note Documents</label>
                  <input type="file" multiple name="delivery_docs[]" class="form-control dark-text">
                </div>

              </div>

              <div class="row">

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select class="form-control ms supplier-select" readonly data-live-search="true" data-style="text-white" required name="supplier_id">
                      <option value="{{ $purchaseorder->supplier->id }}">
                        {{ $purchaseorder->supplier->supplier_name }}
                      </option>
                    </select>
                  </div>
                </div>

              </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="supplier">Warehouse</label>
                    <select class="form-control ms searchable-select warehouse-select" data-live-search="true" data-style="text-white" required name="warehouse_id" onchange="filterFloors(this)">
                      <option value="" disabled selected>Choose A Warehouse</option>
                      @foreach($warehouses as $warehouse)
                      <option value="{{ $warehouse->id }}">
                        {{ $warehouse->warehouse_name }} - ({{ $warehouse->location->location_name }})
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="supplier">Floor</label>
                    <select class="form-control searchable-select ms floor-select" data-live-search="true" data-style="text-white" required name="floor_id" onchange="filterShelves(this)">
                    </select>
                  </div>
                </div>

              </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="supplier">Shelf</label>
                    <select class="form-control searchable-select shelf-select ms" data-live-search="true" data-style="text-white" required name="shelf_id" onchange="filterBins(this)">
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="supplier">Bins</label>
                    <select class="form-control searchable-select bin-select ms" data-live-search="true" data-style="text-white" required name="bin_id">
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 mt-1 text-center">
                <h3 class="text-underline mb-0">Item List</h3>

                @foreach($purchaseorder->purchaseorderitems as $purchaseorderitem)
                <div class="col-sm-12 purchase-order-item border-top border-bottom border-info">
                  <input type="hidden" name="purchase_order_item[]" value="">
                  <input type="hidden" name="item_type[]" value="{{ $purchaseorderitem->item_type }}">
                  @if($purchaseorderitem->item_type == "POWDER")
                  @if($purchaseorderitem->new_item_name)
                  <input type="hidden" name="item_id[]" value="">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" checked disabled class="form-check-input" name="new_item[]" value="">Treat as new item
                    </label>
                  </div>
                  <div class="d-flex flex-column">
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="color">Color</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->new_item_name }}" name="powder_color" class="form-control" id="color" class="dark-text" placeholder="Enter powder color">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="code">Code</label>
                          <input type="text" required name="powder_code" class="form-control" id="code" class="dark-text" placeholder="Enter powder code">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="description">Description</label>
                          <input type="text" required name="powder_description" class="form-control" id="description" class="dark-text" placeholder="Enter powder description">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="serial">Serial Number</label>
                          <input type="text" required name="serial_no" class="form-control" id="serial" class="dark-text" placeholder="Enter serial number">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="manufactureDate">Manufacture Date</label>
                          <input type="date" required name="manufacture_date" class="form-control" id="manufactureDate" class="dark-text">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="expiryDate">Expiry Date</label>
                          <input type="date" required name="expiry_date" class="form-control" id="expiryDate" class="dark-text">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="goodsWeight">Goods Weight</label>
                          <input type="number" min="0" step=".01" required name="goods_weight" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="batchNo">Batch Number</label>
                          <input type="text" required name="batch_no" class="form-control" id="batchNo" class="dark-text" placeholder="Enter goods weight">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Cost(With VAT)</label>
                          <input type="number" readonly value="{{ round(($purchaseorderitem->cost + $purchaseorderitem->vat_addition),2) }}" min="0" step=".01" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Cost VAT</label>
                          <input type="number" readonly value="{{ $purchaseorderitem->vat }}" min="0" step=".01" max="100" name="standard_cost_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Price(With VAT)</label>
                          <input type="number" min="0" step=".01" required name="standard_price" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Price VAT</label>
                          <input type="number" min="0" step=".01" max="100" name="standard_price_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="minThreshold">Min Threshold</label>
                          <input type="number" min="0" step=".01" required name="min_threshold" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="maxThreshold">Max Threshold</label>
                          <input type="number" min="0" step=".01" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label>Weight Added</label>
                          <input type="number" min="0" step=".01" max="100" name="weight_added[]" class="form-control" class="dark-text" readonly value="{{ $purchaseorderitem->quantity }}">
                        </div>
                      </div>
                    </div>
                  </div>
                  @else
                  <input type="hidden" name="item_id[]" value="{{ $purchaseorderitem->powder_id }}">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" name="new_item[]" value="">Treat as new item
                    </label>
                  </div>
                  <div class="d-flex flex-column">
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="color">Color</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->powder->powder_color }}" name="powder_color" class="form-control" id="color" class="dark-text" placeholder="Enter powder color">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="code">Code</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->powder->powder_code }}" name="powder_code" class="form-control" id="code" class="dark-text" placeholder="Enter powder code">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="description">Description</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->powder->powder_description }}" name="powder_description" class="form-control" id="description" class="dark-text" placeholder="Enter powder description">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="serial">Serial Number</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->powder->serial_no }}" name="serial_no" class="form-control" id="serial" class="dark-text" placeholder="Enter serial number">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="manufactureDate">Manufacture Date</label>
                          <input type="date" required readonly value="{{ $purchaseorderitem->powder->manufacture_date }}" name="manufacture_date" class="form-control" id="manufactureDate" class="dark-text">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="expiryDate">Expiry Date</label>
                          <input type="date" required readonly value="{{ $purchaseorderitem->powder->expiry_date }}" name="expiry_date" class="form-control" id="expiryDate" class="dark-text">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="goodsWeight">Goods Weight</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->powder->goods_weight }}" name="goods_weight" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="batchNo">Batch Number</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->powder->batch_no }}" name="batch_no" class="form-control" id="batchNo" class="dark-text" placeholder="Enter goods weight">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Cost(With VAT)</label>
                          <input type="number" readonly value="{{ round(($purchaseorderitem->cost + $purchaseorderitem->vat_addition),2) }}" min="0" step=".01" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Cost VAT</label>
                          <input type="number" readonly value="{{ $purchaseorderitem->vat }}" min="0" step=".01" max="100" name="standard_cost_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Price(With VAT)</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->powder->standard_price }}" name="standard_price" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Price VAT</label>
                          <input type="number" min="0" readonly value="{{ $purchaseorderitem->powder->standard_price_vat }}" step=".01" max="100" name="standard_price_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="minThreshold">Min Threshold</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->powder->min_threshold }}" name="min_threshold" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="maxThreshold">Max Threshold</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->powder->max_threshold }}" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label>Weight Added</label>
                          <input type="number" min="0" step=".01" max="100" name="weight_added[]" class="form-control" class="dark-text" readonly value="{{ $purchaseorderitem->quantity }}">
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
                  @elseif($purchaseorderitem->item_type == "NON INVENTORY")
                  @if($purchaseorderitem->new_item_name)
                  <input type="hidden" name="item_id[]" value="">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" checked disabled class="form-check-input" name="new_item[]" value="">Treat as new item
                    </label>
                  </div>
                  <div class="d-flex flex-column">

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="itemName">Name</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->new_item_name }}" name="item_name" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Cost(With VAT)</label>
                          <input type="number" min="0" step=".01" readonly value="{{ round(($purchaseorderitem->cost + $purchaseorderitem->vat_addition),2) }}" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">VAT</label>
                          <input type="number" min="0" step=".01" max="100" readonly value="{{ $purchaseorderitem->vat }}" name="standard_cost_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Quantity</label>
                          <input type="number" min="0" step=".01" readonly value="{{ $purchaseorderitem->quantity }}" name="quantity_added" class="form-control" id="vat" class="dark-text">
                        </div>
                      </div>
                    </div>
                  </div>
                  @else
                  <input type="hidden" name="item_id[]" value="{{ $purchaseorderitem->non_inventory_item_id }}">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" name="new_item[]" value="">Treat as new item
                    </label>
                  </div>
                  <div class="d-flex flex-column">

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="itemName">Name</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->noninventoryitem->item_name }}" name="item_name" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Cost(With VAT)</label>
                          <input type="number" min="0" step=".01" readonly value="{{ round(($purchaseorderitem->cost + $purchaseorderitem->vat_addition),2) }}" required name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">VAT</label>
                          <input type="number" min="0" step=".01" max="100" readonly value="{{ $purchaseorderitem->vat }}" name="standard_cost_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Quantity</label>
                          <input type="number" min="0" step=".01" readonly value="{{ $purchaseorderitem->quantity }}" name="quantity_added" class="form-control" id="vat" class="dark-text">
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
                  @else
                  @if($purchaseorderitem->new_item_name)
                  <input type="hidden" name="item_id[]" value="">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" checked disabled class="form-check-input" name="new_item[]" value="">Treat as new item
                    </label>
                  </div>
                  <div class="d-flex flex-column">

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="itemName">Name</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->new_item_name }}" name="item_name" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="itemCode">Code</label>
                          <input type="text" required name="item_code" class="form-control" id="itemCode" class="dark-text" placeholder="Enter item code">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="itemDescription">Description</label>
                          <input type="text" required name="item_description" class="form-control" id="itemDescription" class="dark-text" placeholder="Enter item description">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="serial">Serial</label>
                          <input type="text" required name="serial_no" class="form-control" id="serial" class="dark-text" placeholder="Enter item serial number">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="quantityTag">Quantity Tag</label>
                          <input type="text" required name="quantity_tag" class="form-control" id="quantityTag" class="dark-text" placeholder="Enter item quantity tag">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="type">Type</label>
                          <select name="inventory_type" required readonly class="form-control ms">
                            @forelse($inventoryItemTypes as $type)
                            @if(strtoupper($type->humanreadablestring()) == $purchaseorderitem->item_type)
                            <option value="{{ $type->value }}">
                              {{ $type->humanreadablestring() }}
                            </option>
                            @endif
                            @empty
                            <option disabled selected>No types to choose from</option>
                            @endforelse
                          </select>
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="goodsWeight">Goods Weight</label>
                          <input type="number" min="0" step=".0001" required name="goods_weight" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Cost(With VAT)</label>
                          <input type="number" min="0" step=".01" required name="standard_cost" readonly value="{{ round(($purchaseorderitem->cost + $purchaseorderitem->vat_addition),2) }}" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Cost VAT</label>
                          <input type="number" min="0" step=".01" max="100" name="standard_cost_vat" readonly value="{{ $purchaseorderitem->vat }}" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Price(With VAT)</label>
                          <input type="number" min="0" step=".01" required name="standard_price" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Price VAT</label>
                          <input type="number" min="0" step=".01" max="100" name="standard_price_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="minThreshold">Min Threshold</label>
                          <input type="number" min="0" step=".01" required name="min_threshold" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="maxThreshold">Max Threshold</label>
                          <input type="number" min="0" step=".01" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="openingQuantity">Added Quantity</label>
                          <input type="number" min="0" step=".01" name="quantity_added[]" class="form-control" id="openingQuantity" class="dark-text" readonly value="{{ $purchaseorderitem->quantity }}">
                        </div>
                      </div>
                    </div>
                  </div>
                  @else
                  <input type="hidden" name="item_id[]" value="{{ $purchaseorderitem->inventory_item_id }}">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" name="new_item[]" value="">Treat as new item
                    </label>
                  </div>
                  <div class="d-flex flex-column">

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="itemName">Name</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->inventoryitem->item_name }}" name="item_name" class="form-control" id="itemName" class="dark-text" placeholder="Enter item name">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="itemCode">Code</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->inventoryitem->item_code }}" name="item_code" class="form-control" id="itemCode" class="dark-text" placeholder="Enter item code">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="itemDescription">Description</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->inventoryitem->item_description }}" name="item_description" class="form-control" id="itemDescription" class="dark-text" placeholder="Enter item description">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="serial">Serial</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->inventoryitem->serial_no }}" name="serial_no" class="form-control" id="serial" class="dark-text" placeholder="Enter item serial number">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="quantityTag">Quantity Tag</label>
                          <input type="text" required readonly value="{{ $purchaseorderitem->inventoryitem->quantity_tag }}" name="quantity_tag" class="form-control" id="quantityTag" class="dark-text" placeholder="Enter item quantity tag">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="type">Type</label>
                          <select name="inventory_type" required readonly class="form-control ms">
                            @forelse($inventoryItemTypes as $type)
                            @if(strtoupper($type->humanreadablestring()) == $purchaseorderitem->item_type)
                            <option value="{{ $type->value }}">
                              {{ $type->humanreadablestring() }}
                            </option>
                            @endif
                            @empty
                            <option disabled selected>No types to choose from</option>
                            @endforelse
                          </select>
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="goodsWeight">Goods Weight</label>
                          <input type="number" min="0" step=".0001" required readonly value="{{ $purchaseorderitem->inventoryitem->goods_weight }}" name="goods_weight" class="form-control" id="goodsWeight" class="dark-text" placeholder="Enter goods weight">
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Cost(With VAT)</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->inventoryitem->standard_cost }}" name="standard_cost" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard cost">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Cost VAT</label>
                          <input type="number" min="0" step=".01" max="100" readonly value="{{ $purchaseorderitem->inventoryitem->standard_cost_vat }}" name="standard_cost_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for cost">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="standardCost">Standard Price(With VAT)</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->inventoryitem->standard_price }}" name="standard_price" class="form-control" id="standardCost" class="dark-text" placeholder="Enter standard price">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="vat">Standard Price VAT</label>
                          <input type="number" min="0" step=".01" max="100" readonly value="{{ $purchaseorderitem->inventoryitem->standard_price_vat }}" name="standard_price_vat" class="form-control" id="vat" class="dark-text" placeholder="Enter applicable VAT charge for price">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="minThreshold">Min Threshold</label>
                          <input type="number" min="0" step=".01" required readonly value="{{ $purchaseorderitem->inventoryitem->min_threshold }}" name="min_threshold" class="form-control" id="minThreshold" class="dark-text" placeholder="Enter minimum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="maxThreshold">Max Threshold</label>
                          <input type="number" min="0" step=".01" readonly value="{{ $purchaseorderitem->inventoryitem->max_threshold }}" name="max_threshold" class="form-control" id="maxThreshold" class="dark-text" placeholder="Enter maximum threshold">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="openingQuantity">Added Quantity</label>
                          <input type="number" min="0" step=".01" name="quantity_added[]" class="form-control" id="openingQuantity" class="dark-text" readonly value="{{ $purchaseorderitem->quantity }}">
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
                  @endif
                </div>
                @endforeach
              </div>

              <button type="button" onclick="populateAggregateFields(this)" name="submit_btn" value="Complete Purchase Order" class="btn btn-success text-white w-100">
                COMPLETE PURCHASE ORDER
              </button>

            </form>


          </div>

          <script type="text/javascript">
            const floorOptions = <?php echo json_encode($floors) ?>;

            const shelfOptions = <?php echo json_encode($shelves) ?>;

            const binOptions = <?php echo json_encode($bins) ?>;

            let invoiceDocs;
            let deliveryDocs;
          </script>
          @include('universal-layout.scripts',
          [
          'libscripts' => true,
          'vendorscripts' => true,
          'mainscripts' => true,
          'jquery' => true,
          'sweetalert' => true,
          'select2' => true,
          ]
          )
          <script src="/assets/js/custom/complete-po.min.js" charset="utf-8"></script>
          @include('universal-layout.footer')