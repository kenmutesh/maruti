@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Stock In',
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

        <form class="" onsubmit="showSpinner(event)" action="{{ route('stock_in_aluminium') }}" method="post" autocomplete="off" class="container" enctype="multipart/form-data">
          @csrf
          <div class="row">

            <div class="col-sm-6">
              <label>LPO Number</label>
              <input type="text" required name="lpo_ref" class="form-control dark-text">
            </div>

            <div class="col-sm-6">
              <label>LPO Documents</label>
              <input type="file" multiple name="lpo_docs[]" class="form-control dark-text">
            </div>

          </div>

          <div class="row">

            <div class="col-sm-6">
              <label>Invoice Number</label>
              <input type="text" required name="invoice_ref" class="form-control dark-text">
            </div>

            <div class="col-sm-6">
              <label>Invoice Documents</label>
              <input type="file" multiple name="invoice_docs[]" class="form-control dark-text">
            </div>

          </div>

          <div class="row">

            <div class="col-sm-6">
              <label>Delivery Note Number</label>
              <input type="text" required name="delivery_ref" class="form-control dark-text">
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

          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="supplier">Warehouse</label>
                @if(session()->get('auth_warehouse_uid') == 'N/A')
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#createWarehouseForm">
                  Add Warehouse
                </button>
                @endif
                <select class="form-control searchable-select warehouse-select" required name="warehouse_id" onchange="filterFloors(this)">
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
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label for="supplier">Floor</label>
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#createFloorForm">
                  Add Floor
                </button>
                <select class="form-control searchable-select floor-select" required name="floor_id" onchange="filterShelves(this)">
                </select>
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="supplier">Shelf</label>
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#createShelfForm">
                  Add Shelf
                </button>
                <select class="form-control searchable-select shelf-select" required name="shelf_id" onchange="filterBins(this)">
                </select>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label for="supplier">Bins</label>
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#creatBinsForm">
                  Add Bin
                </button>
                <select class="form-control searchable-select bin-select" required name="bin_id">
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="supplier">Vehicle Plate</label>
                <input type="text" class="form-control dark-text" name="vehicle_plate" value="">
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label for="supplier">Start Mileage</label>
                <input type="text" class="form-control dark-text" name="start_mileage" value="">
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label for="supplier">Stop Mileage</label>
                <input type="text" class="form-control dark-text" name="stop_mileage" value="">
              </div>
            </div>
          </div>

          <div class="row mx-auto mt-5 text-center">
            <div class="col-sm-12 border border-top-0 border-left-0 border-right-0 mb-3 item-in-list" attr-inventory-type="ALUMINIUM">
              <div class="col-sm-12 border border-lg border-primary border-right-0 border-left-0 border-bottom-0 p-3">
                Inventory Type: ALUMINIUM
              </div>
              <input type="hidden" name="inventory_type" value="ALUMINIUM"/>
              <input type="hidden" name="item_id" value="<?php echo $aluminiumItem->id ?>"/>
              <input type="hidden" name="code" value="<?php echo $aluminiumItem->item_code ?>"/>
              <input type="hidden" name="description" value="<?php echo $aluminiumItem->item_description ?>"/>
            </div>

            <?php
              if (true) {
                // zero Quantity ITEM

                ?>
                <input type="hidden" name="item_status" value="NEW">
                <div class="col-sm-6 form-group">
                  <label>Name</label>
                  <input type="text" required name="item_name" class="form-control dark-text" readonly value="<?php echo $aluminiumItem->item_name ?>">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Serial Number</label>
                  <input type="text" required name="serial_number" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Quantity Tag</label>
                  <select class="form-control" name="quantity_tag">
                    <option value="UNITS">UNITS</option>
                    <option value="ROLL">ROLL</option>
                    <option value="PIECES">PIECES</option>
                  </select>
                </div>


                <div class="col-sm-6 form-group">
                  <label>Weight of Good</label>
                  <input type="text" required name="good_weight" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Stocked in quantity</label>
                  <input type="text" required name="stocked_quantity" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Minimum Threshold</label>
                  <input type="text" required name="min_threshold" onblur="checkMinThreshold(this)" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Maximum Threshold</label>
                  <input type="text" onblur="checkMaxThreshold(this)" required name="max_threshold" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Unit Cost</label>
                  <input type="text" required name="unit_cost" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Item Price</label>
                  <input type="text" required name="item_price" onkeyup="updateTaxedPrice(this)" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Item Tax(%)</label>
                  <input type="text" required name="item_tax" onkeyup="updateTaxedPrice(this)" class="form-control dark-text">
                </div>

                <div class="col-sm-6 form-group">
                  <label>Price + Tax(%)</label>
                  <input type="text" readonly required name="taxed_price" class="form-control dark-text">
                </div>
                <?php
              }
            ?>


          </div>

          <button type="submit" name="submit_btn" value="RECORD STOCK IN" class="btn btn-info text-white form-control mx-auto">
            RECORD STOCK IN
          </button>

        </form>
        </div>

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


        <!-- modal for creating warehouses on the fly -->
        <div class="modal fade" id="createWarehouseForm" tabindex="-1" role="dialog" aria-labelledby="createWarehouseForm" aria-hidden="true">
          <div class="modal-dialog" style="margin: -100px auto 1.75rem auto;" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create A Warehouse in the System</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="tim-icons icon-simple-remove"></i>
                </button>
              </div>
              <div class="modal-body">

                <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('warehouses.store') }}">
                  @csrf
                  <div class="d-flex flex-column">

                    <div class="col">
                      <div class="form-group">
                        <label for="locationID">Location Name</label>
                        <select required name="location_id" class="form-control" id="locationID">
                            @foreach($locations as $singleLocation)
                              <option value="{{ $singleLocation->id }}">{{ $singleLocation->location_name }}</option>
                            @endforeach
                        </select>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="warehouseName">Warehouse Name</label>
                        <input type="text" required name="warehouse_name" class="form-control" id="warehouseName" aria-describedby="warehouseName" class="dark-text" placeholder="Enter warehouse name">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="warehouseDescription">Warehouse Description</label>
                        <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="warehouse_description" class="form-control" rows="3" required id="warehouseDescription"></textarea>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label>Warehouse Status</label>
                        <select class="form-control" name="warehouse_status" >
                          <option value="ACTIVE">Active</option>
                          <option value="INACTIVE">Inactive</option>
                        </select>
                      </div>
                    </div>

                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="button" onclick="addWarehouseViaAPI(this)" name="submit_btn" value="Create Warehouse" class="btn btn-primary">CREATE WAREHOUSE</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- modal for creating floor on the fly -->
        <div class="modal fade" id="createFloorForm" tabindex="-1" role="dialog" aria-labelledby="createFloorForm" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create A Floor in the System</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="tim-icons icon-simple-remove"></i>
                </button>
              </div>
              <div class="modal-body">

                <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('floors.store') }}">
                  @csrf
                  <div class="d-flex flex-column">

                    <div class="col">
                      <div class="form-group">
                        <label for="floorName">Floor Name</label>
                        <input type="text" required name="floor_name" class="form-control" id="floorName" aria-describedby="locationName" class="dark-text" placeholder="Enter floor name">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="warehouseID">Warehouse Name</label>
                        <select required name="warehouse_id" class="form-control" id="warehouseID">
                            @foreach($warehouses as $singleWarehouse)
                              <option value="{{ $singleWarehouse->id }}">{{ $singleWarehouse->warehouse_name }} ( {{ $singleWarehouse->location->location_name }})</option>
                            @endforeach
                        </select>
                      </div>
                    </div>

                  </div>


              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="button" onclick="addFloorViaAPI(this)" name="submit_btn" name="submit_btn" value="Create Floor" class="btn btn-primary">CREATE FLOOR</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- modal for creating shelf on the fly -->
        <div class="modal fade" id="createShelfForm" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create A Shelf in the System</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="tim-icons icon-simple-remove"></i>
                </button>
              </div>
              <div class="modal-body">

                <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('shelves.store') }}">
                  @csrf
                  <div class="d-flex flex-column">

                    <div class="col">
                      <div class="form-group">
                        <label for="shelfName">Shelf Name</label>
                        <input type="text" required name="shelf_name" class="form-control" id="shelfName" aria-describedby="shelfName" class="dark-text" placeholder="Enter shelf name">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="floorID">Floor</label>
                        <select required name="floor_id" class="form-control" id="floorID">
                            @foreach($floors as $singleFloor)
                              <option value="{{ $singleFloor->id }}">
                                {{ $singleFloor->floor_name }} ( {{ $singleFloor->warehouse->warehouse_name }})
                              </option>
                            @endforeach
                        </select>
                      </div>
                    </div>

                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="button" onclick="addShelfViaAPI(this)" name="submit_btn" value="Create Shelf" class="btn btn-primary">CREATE SHELF</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal for creating bins on the fly -->
        <div class="modal fade" id="creatBinsForm" tabindex="-1" role="dialog" aria-labelledby="createWarehouseForm" aria-hidden="true">
          <div class="modal-dialog" style="margin: -100px auto 1.75rem auto;" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create A Bin in the System</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="tim-icons icon-simple-remove"></i>
                </button>
              </div>
              <div class="modal-body">

                <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('bins.store') }}">
                  @csrf
                  <div class="d-flex flex-column">

                    <div class="col">
                      <div class="form-group">
                        <label for="shelfID">Shelf Name</label>
                        <select required name="shelf_id" class="form-control shelf-select" id="shelfID">
                            @foreach($shelves as $singleShelf)
                              <option value="{{ $singleShelf->id }}">
                                {{ $singleShelf->shelf_name }} ( {{ $singleShelf->floor->floor_name }})
                              </option>
                            @endforeach
                        </select>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="binName">Bin Name</label>
                        <input type="text" required name="bin_name" class="form-control" id="binName" aria-describedby="binName" class="dark-text" placeholder="Enter bin name">
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label for="binDescription">Bin Description</label>
                        <textarea style="border: 1px solid #2b3553;border-radius: .25rem;" name="bin_description" class="form-control" rows="3" required id="binDescription"></textarea>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group">
                        <label>Bin Status</label>
                        <select class="form-control" name="bin_status" >
                          <option value="ACTIVE">Active</option>
                          <option value="INACTIVE">Inactive</option>
                        </select>
                      </div>
                    </div>

                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="button" onclick="addBinViaAPI(this)" name="submit_btn" value="Create Bin" class="btn btn-primary">CREATE BIN</button>
                </form>
              </div>
            </div>
          </div>
        </div>


      </div>

      <script type="text/javascript">

          const floorOptions = <?php echo json_encode($floors) ?>;

          const shelfOptions = <?php echo json_encode($shelves) ?>;

          const binOptions = <?php echo json_encode($bins) ?>;

          let stockInValid = true;
          let invoiceDocs;
          let deliveryDocs;

      </script>
      <script src="/assets/js/custom/complete-po.min.js" charset="utf-8"></script>
      @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'bootstrapselect' => true,
   
  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')
