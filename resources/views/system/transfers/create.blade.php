
@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Aprotec',
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

        <form class="" onsubmit="showSpinner(event)" action="{{ route('store_transfer') }}" method="post" autocomplete="off" enctype="multipart/form-data">
          @csrf

          <div class="row">

            <div class="col-sm-6">
              <label>Requested By</label>
              <input type="hidden" name="requested_by" value="{{ $currentlyLogged->id }}">
              <input type="text" readonly name="username" class="form-control" value="{{ $currentlyLogged->username }}">
            </div>

            <div class="col-sm-6">
              <label>Note</label>
              <input type="text" required name="note" class="form-control dark-text">
            </div>

          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="supplier">Destination Warehouse</label>
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
                <label for="supplier">Destination Floor</label>
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
                <label for="supplier">Destination Shelf</label>
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#createShelfForm">
                  Add Shelf
                </button>
                <select class="form-control searchable-select shelf-select" required name="shelf_id" onchange="filterBins(this)">
                </select>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label for="supplier">Destination Bin</label>
                <button type="button" name="button" class="btn btn-primary p-1" data-toggle="modal" data-target="#creatBinsForm">
                  Add Bin
                </button>
                <select class="form-control searchable-select bin-select" required name="bin_id">
                </select>
              </div>
            </div>
          </div>

          <div class="row mt-3 pl-4 w-80 table-responsive" style="overflow-x: auto;display:block;overflow:initial;">
            <table class="table table-bordered w-70 mx-auto table-fixed-2">
              <th class="p-0 border">Item</th>
              <th class="p-0 border">Color/Name</th>
              <th class="p-0 border">Code</th>
              <th class="p-0 border">Description</th>
              <th class="p-0 border">Quantity Transferred</th>
              <th class="p-0 border">KG Transferred</th>
              <th class="p-0 border">Action</th>
              <tbody class="item-list">
                <tr>
                  <td>
                    <input type="hidden" name="item_id[]" value="">
                    <input type="hidden" name="inventory_type[]" value="">
                    <input type="hidden" name="item_warehouse_id[]" value="">
                    <input type="hidden" name="item_bin_id[]" value="">

                    <select style="width:200px;" class="searchable-select" onchange="prefillItemTransfer(this)" data-live-search="true">
                      <option disabled selected>Choose from inventory</option>
                      <optgroup label="POWDER">
                        <?php
                          foreach ($powderInventory as $powderItem) {
                            if ($powderItem->total_weight < 1) {
                              continue;
                            }
                        ?>
                          <option value="<?php echo $powderItem->item_id ?>"
                            attr-data-description="<?php echo $powderItem->powder_description ?>"
                            attr-data-code="<?php echo $powderItem->powder_code ?>"
                            attr-data-cost="<?php echo $powderItem->standard_cost ?>"
                            attr-warehouse-id=<?php echo $powderItem->warehouse_id ?>
                            attr-bin-id="<?php echo $powderItem->bin_id ?>"
                            attr-current-quantity="<?php echo $powderItem->total_weight ?>"
                            attr-data-name ="<?php echo $powderItem->powder_color ?>">
                            <?php echo $powderItem->powder_color ?>(<?php echo $powderItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>
                      </optgroup>

                      <optgroup label="HARDWARE">
                        <?php
                          foreach ($hardwareInventory as $hardwareItem) {
                            if ($hardwareItem->total_quantity < 1) {
                              continue;
                            }
                        ?>
                          <option value="<?php echo $hardwareItem->item_id ?>"
                            attr-data-description="<?php echo $hardwareItem->item_description ?>"
                            attr-data-code="<?php echo $hardwareItem->item_code ?>"
                            attr-data-cost="<?php echo $hardwareItem->standard_cost ?>"
                            attr-warehouse-id=<?php echo $hardwareItem->warehouse_id ?>
                            attr-bin-id="<?php echo $hardwareItem->bin_id ?>"
                            attr-current-quantity="<?php echo $hardwareItem->total_quantity ?>"
                            attr-data-name ="<?php echo $hardwareItem->item_name ?>">
                            <?php echo $hardwareItem->item_name ?>(<?php echo $hardwareItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>
                      </optgroup>

                      <optgroup label="ALUMINIUM">
                        <?php
                          foreach ($aluminiumInventory as $aluminiumItem) {
                            if ($aluminiumItem->total_quantity < 1) {
                              continue;
                            }
                        ?>
                          <option value="<?php echo $aluminiumItem->item_id ?>"
                            attr-data-description="<?php echo $aluminiumItem->item_description ?>"
                            attr-data-code="<?php echo $aluminiumItem->item_code ?>"
                            attr-data-cost="<?php echo $aluminiumItem->standard_cost ?>"
                            attr-warehouse-id=<?php echo $aluminiumItem->warehouse_id ?>
                            attr-bin-id="<?php echo $aluminiumItem->bin_id ?>"
                            attr-current-quantity="<?php echo $aluminiumItem->total_quantity ?>"
                            attr-data-name ="<?php echo $aluminiumItem->item_name ?>">
                            <?php echo $aluminiumItem->item_name ?>(<?php echo $aluminiumItem->warehouse_name ?>)
                          </option>
                        <?php
                          }
                        ?>
                      </optgroup>

                    </select>
                  </td>

                  <td>
                    <input type="text" class="w-100" name="item_name[]" value="">
                  </td>

                  <td>
                    <input type="text" class="w-100" name="item_code[]" value="">
                  </td>

                  <td>
                    <input type="text" class="w-100" name="item_description[]" value="">
                  </td>

                  <td>
                    <input type="number" class="w-100" name="item_qty[]" value="">
                  </td>

                  <td>
                    <input type="number" class="w-100" name="item_kg[]" value="">
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

          <button type="submit" name="submit_btn" value="MAKE TRANSFER" class="btn btn-success w-100 text-white">
            MAKE TRANSFER
          </button>

        </form>
      </div>
    </div>
  </div>
    </div>
</section>

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
                        <select required name="warehouse_id" class="form-control warehouse-select" id="warehouseID">
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
                        <select required name="floor_id" class="form-control floor-select" id="floorID">
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
   
  'tableAction' => true,
  ]
  )
  @include('universal-layout.alert')
  @include('universal-layout.footer')
