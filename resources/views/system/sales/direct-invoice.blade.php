@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Powder Coating',
'select2bs4' => true,
]
)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
    .select2-container .select2-results__option[aria-disabled=true] {
        display: none;
    }
</style>

<body class="theme-green">
    @include('universal-layout.spinner')

    @include('universal-layout.system-sidemenu',
    [
    'slug' => '/coatingjobs/create'
    ]
    )
    <section class="content home">
        <div class="container-fluid">
            <div class="wrapper">
                <div class="main-panel">

                    <div class="content">

                        <form class="" onsubmit="showSpinner(event)" action="{{ route('direct_invoice') }}" method="post" autocomplete="off">
                            @csrf
                            <input type="hidden" name="quotation_prefix" value="{{ $quotationPrefix }}">
                            <input type="hidden" name="quotation_suffix" value="{{ $quotationSuffix }}">
                            <input type="hidden" name="coating_prefix" value="{{ $coatingJobPrefix }}">
                            <input type="hidden" name="coating_suffix" value="{{ $coatingJobSuffix }}">
                            <input type="hidden" required name="coating_job" value="{{ $coatingJobPrefix }}{{$coatingJobSuffix}}">
                            <input type="hidden" name="quotation_number" class="form-control dark-text" readonly value="{{ $quotationPrefix }}{{ $quotationSuffix }}">
                            <p class="m-0 d-none">
                                Quotation No: <b>{{ $quotationPrefix }}{{ $quotationSuffix }}</b>
                            </p>

                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group position-relative" style="z-index: 2;">
                                        <label for="supplier">Customer</label>
                                        <button type="button" name="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#createCustomerForm">
                                            Add Customer
                                        </button>
                                        <select class="form-control customer-select ms search-select" required name="customer_id" data-live-search="true" onchange="resetJobCards(this)" data-style="text-white">
                                        <option disabled selected>Choose a customer</option>
                                            @foreach($customers as $singleCustomer)
                                            <option value="{{ $singleCustomer->id }}" data-tokens="{{ $singleCustomer->company }}">
                                                {{ $singleCustomer->company }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6 d-flex flex-column justify-content-center">
                                    <label>LPO(Optional)</label>
                                    <input type="text" name="lpo" class="form-control dark-text mt-1">
                                </div>

                                <div class="col-sm-6 my-4">
                                    <label>Date</label>
                                    <input type="date" required name="date" class="form-control dark-text data-today-date">
                                </div>

                                <div class="col-sm-6">
                                      <label class="m-0">Combine Job Cards</label>
                                      <select class="form-control ms search-select" multiple name="combined_jobcards[]">
                                            @foreach($nonPaginated as $coatingJob)
                                              <option value="{{ $coatingJob->id }}" class="job-card-option" data-customer-id="{{ $coatingJob->customer_id }}" disabled>
                                                {{ $coatingJob->coating_suffix }} (KES: {{ $coatingJob->grand_total }}) - ({{ $coatingJob->customer->customer_name }})
                                              </option>
                                            @endforeach
                                      </select>
                                </div>

                                <div class="col-sm-6 my-4">
                                    <label>CU Number</label>
                                    <input type="text" required name="cu_number" class="form-control dark-text" value="011018565000000">
                                </div>

                                <div class="col-sm-3 my-4 d-none">
                                    <label>In Date(Optional)</label>
                                    <input type="date" name="in_date" class="form-control dark-text">
                                </div>

                                <div class="col-sm-3 my-4 d-none">
                                    <label>Out Date(Optional)</label>
                                    <input type="date" name="out_date" class="form-control dark-text">
                                </div>

                                <div class="col-sm-3 my-4 d-none">
                                    <label>Ready Date(Optional)</label>
                                    <input type="date" name="ready_date" class="form-control dark-text">
                                </div>

                                <div class="col-sm-3 position-relative d-none" style="z-index: 2;">
                                    <label>RAL(Optional)</label>
                                    <input type="hidden" name="ral_warehouse_id" value="">
                                    <input type="hidden" name="ral_bin_id" value="">
                                    <select class="searchable-select form-control ms search-select" name="ral_main" onchange="prefillRALData(this)" data-live-search="true" data-style="text-white">
                                        <option disabled selected>Pick Powder Item</option>
                                        <?php
                                        foreach ($powderInventory as $powderItem) {
                                        ?>
                                            <option value="<?php echo $powderItem->item_id ?>" attr-data-description="<?php echo $powderItem->powder_description ?>" attr-data-code="<?php echo $powderItem->powder_code ?>" attr-data-cost="<?php echo $powderItem->standard_cost ?>" attr-data-color="<?php echo $powderItem->powder_color ?>" attr-warehouse-id="<?php echo $powderItem->warehouse_id ?>" attr-bin-id="<?php echo $powderItem->bin_id ?>">
                                                <?php echo $powderItem->powder_color ?> -
                                                (<?php echo $powderItem->warehouse_name ?>) -
                                                (<?php echo $powderItem->supplier_name ?>)
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-sm-3 d-none">
                                    <label>Goods Weight(Optional)</label>
                                    <input type="text" name="goods_weight" onkeyup="calculatePowderEstimateAluminium(this)" class="form-control dark-text p-2">
                                </div>

                                <div class="col-sm-3 d-none">
                                    <div class="form-group">
                                        <label for="profileType">Powder Profile Type</label>
                                        <select id="profileType" name="profile_type" class="form-control ms p-2" onchange="calculatePowderEstimateAluminium(this)">
                                            <option value="N/A">N/A</option>
                                            <option value="Heavy">Heavy</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Light">Light</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3 d-none">
                                    <label>Powder Estimate</label>
                                    <input type="text" required readonly name="powder_estimate" class="form-control dark-text p-2">
                                </div>

                                <input readonly type="hidden" name="color" class="form-control dark-text">
                                <input readonly type="hidden" multiple name="code" class="form-control dark-text">

                                <div class="col-sm-6 my-4">
                                    <label>Grand Total</label>
                                    <input type="text" name="grand_total" readonly class="form-control" value="">
                                </div>

                            </div>

                            <div class="row my-4">

                                <div class="col-sm-12 d-none flex-column">
                                    <label>Product</label>
                                    <div>
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="belongs_to" id="maruti" onchange="toggleActiveItemList(this)" value="MARUTI" checked> MARUTI
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="belongs_to" id="owner" onchange="toggleActiveItemList(this)" value="OWNER-COMBINED"> OWNER COMBINED
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="belongs_to" id="owner" onchange="toggleActiveItemList(this)" value="OWNER-ALUMINIUM"> OWNER ALUMINIUM
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="belongs_to" id="owner" onchange="toggleActiveItemList(this)" value="OWNER-STEEL"> OWNER STEEL
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="belongs_to" id="owner" onchange="toggleActiveItemList(this)" value="DIRECT-SALE"> DIRECT SALE
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="table-responsive mt-3 w-80 maruti-coating" style="overflow-x: auto;display:block;overflow:initial;">
                                <table class="table table-bordered col w-70 mx-auto">
                                    <th class="p-0 border text-center">Item</th>
                                    <th class="p-0 border text-center">Description</th>
                                    <th class="p-0 border text-center">Quantity</th>
                                    <th class="p-0 border text-center">Boxes</th>
                                    <th class="p-0 border text-center">Unit Cost</th>
                                    <th class="p-0 border text-center">Vat Inclusive</th>
                                    <th class="p-0 border text-center">Total Amt</th>
                                    <th class="p-0 border text-center">Action</th>
                                    <tbody class="item-list maruti-table">
                                        <tr>
                                            <td>
                                                <input type="hidden" name="item_id[]" value="">
                                                <input type="hidden" name="inventory_type[]" value="">
                                                <input type="hidden" name="warehouse_id[]" value="">
                                                <input type="hidden" name="bin_id[]" value="">
                                                <input type="hidden" onkeyup="calculateItemRowTotal(this)" name="item_tax[]" value="<?php echo $vat->percentage ?? '' ?>">

                                                <select style="width:200px;z-index: 2;" class="search-select ms position-relative" onchange="prefillItemRow(this, true, true, true)" data-style="text-white position-static" data-live-search="true" data-boundary="viewport">
                                                    <option disabled selected>Choose from inventory</option>
                                                    <option value="Custom">Custom</option>
                                                    <optgroup label="HARDWARE">
                                                        <?php
                                                        foreach ($hardwareInventory as $hardwareItem) {
                                                        ?>
                                                            <option value="<?php echo $hardwareItem->item_id ?>" attr-data-description="<?php echo $hardwareItem->item_description ?>" 
                                                            attr-data-code="<?php echo $hardwareItem->item_code ?>" 
                                                            attr-data-cost="<?php echo $hardwareItem->standard_cost ?>" attr-current-quantity="<?php echo $hardwareItem->current_quantity ?>" attr-min-threshold="<?php echo $hardwareItem->min_threshold ?>" attr-data-name="<?php echo $hardwareItem->item_name ?>" attr-warehouse-id="<?php echo $hardwareItem->warehouse_id ?>" attr-bin-id="<?php echo $hardwareItem->bin_id ?>" attr-weight="<?php echo $hardwareItem->weight_per_kg ?>">
                                                                <?php echo $hardwareItem->item_name ?>-(<?php echo $hardwareItem->current_quantity . " " . $hardwareItem->quantity_tag ?>)
                                                                - (<?php echo $hardwareItem->warehouse_name ?>)
                                                            </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </optgroup>

                                                    <optgroup label="ALUMINIUM">
                                                        <?php
                                                        foreach ($aluminiumInventory as $aluminiumItem) {
                                                        ?>
                                                            <option value="<?php echo $aluminiumItem->item_id ?>" attr-data-description="<?php echo $aluminiumItem->item_description ?>" attr-data-code="<?php echo $aluminiumItem->item_code ?>" attr-data-cost="<?php echo $aluminiumItem->standard_cost ?>" attr-current-quantity="<?php echo $aluminiumItem->current_quantity ?>" attr-min-threshold="<?php echo $aluminiumItem->min_threshold ?>" attr-data-name="<?php echo $aluminiumItem->item_name ?>" attr-warehouse-id="<?php echo $aluminiumItem->warehouse_id ?>" attr-bin-id="<?php echo $aluminiumItem->bin_id ?>" attr-weight="<?php echo $aluminiumItem->weight_per_kg ?>">
                                                                <?php echo $aluminiumItem->item_name ?>-(<?php echo $aluminiumItem->current_quantity . " " . $aluminiumItem->quantity_tag ?>)
                                                                - (<?php echo $aluminiumItem->warehouse_name ?>)
                                                            </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </optgroup>

                                                </select>
                                            </td>

                                            <input type="hidden" readonly name="item_name[]" value="">
                                            <input type="hidden" readonly name="item_code[]" value="">
                                            <input type="hidden" class="w-100" readonly onkeyup="calculateItemRowTotal(this)" name="item_kg[]" value="">

                                            <td>
                                                <input type="text" name="item_description[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" min="1" class="w-100" onkeyup="calculateItemRowTotal(this, true, true)" oninput="calculateItemRowTotal(this, true, true)" name="item_qty[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" class="w-100" name="item_boxes[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" class="w-100" onkeyup="calculateItemRowTotal(this)" name="unit_cost[]" value="">
                                            </td>

                                            <td>
                                                <select class="ms w-100" name="vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" class="maruti-total w-100" name="amount[]" readonly>
                                            </td>

                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                <button type="button" name="button" onclick="addItemRow('.maruti-table')" class="btn btn-danger">ADD
                                                    ITEM</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-3 w-80 owner-aluminium-coating" style="overflow-x: auto;display:none;">
                                <table class="table table-bordered col w-70 mx-auto">
                                    <th class="p-0 border text-center">Description</th>
                                    <th class="p-0 border text-center">Qty</th>
                                    <th class="p-0 border text-center">KG</th>
                                    <th class="p-0 border text-center">Unit Cost</th>
                                    <th class="p-0 border text-center">Vat Inclusive</th>
                                    <th class="p-0 border text-center">Amount</th>
                                    <th class="p-0 border text-center">Action</th>
                                    <tbody class="item-list">
                                        <tr class="clients-tr">
                                            <td>
                                                <input type="hidden" name="item_id[]">
                                                <input type="hidden" name="inventory_type[]">
                                                <input type="hidden" name="warehouse_id[]">
                                                <input type="hidden" name="bin_id[]">
                                                <input type="hidden" name="item_code[]" value="">
                                                <input type="hidden" name="item_tax[]" value="">
                                                <input type="hidden" name="item_name[]" value="">
                                                <input type="text" name="item_description[]" class="w-100" value="">
                                            </td>
                                            <td><input type="number" name="item_qty[]" class="w-100" value="0" min="0"></td>
                                            <td><input type="number" class="w-100 owner-kg" onkeyup="updateAmountOwnerUnits(this)" onchange="updateAmountOwnerUnits(this)"" name="item_kg[]" value="" min="0"></td>
                                            <td>
                                                <input type="text" class="w-100" onkeyup="updateAmountOwnerUnits(this)" name="unit_cost[]" value="">
                                            </td>
                                            <td>
                                                <select class="ms w-100" name="vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="unit-cost-coating owner-total w-100" readonly name="amount[]" value="">
                                            </td>
                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-5 w-80 owner-steel-coating" style="overflow-x: auto;display:block;overflow:initial;">
                                <p class="text-center w-100 font-weight-bold h3">
                                    STEEL COATING
                                </p>
                                <table class="table table-bordered col w-70 mx-auto p-0">
                                    <th class="p-0 border text-center">Item<br/>Description</th>
                                    <th class="p-0 border text-center"></th>
                                    <th class="p-0 border text-center" style="width: 50px;">RAL</th>
                                    <th class="p-0 border text-center">Unit</th>
                                    <th class="px-2 border text-center">Powder<br/>Estimate</th>
                                    <th class="px-2 border text-center">Linear<br/>Metres</th>
                                    <th class="px-2 border text-center">Square<br/>Metres</th>
                                    <th class="px-2 border text-center">Unit<br/>Cost</th>
                                    <th class="px-2 border text-center">Vat<br/>Inclusive</th>
                                    <th class="p-0 border text-center">Amount</th>
                                    <th class="p-0 border text-center">Action</th>
                                    <tbody class="item-list steel-table">
                                        <tr class="clients-tr">
                                            <td>
                                                <!-- start of hidden inputs essentially to keep the flow -->
                                                <input type="hidden" name="steel_item_id[]">
                                                <input type="hidden" name="steel_inventory_type[]">
                                                <input type="hidden" name="steel_warehouse_id[]">
                                                <input type="hidden" name="steel_bin_id[]">
                                                <input type="hidden" name="steel_item_code[]" value="">
                                                <input type="hidden" name="steel_item_description[]" value="">
                                                <input type="hidden" name="steel_item_tax[]" value="">
                                                <!-- end of hidden input that keep the flow -->
                                                <input type="text" name="steel_item_name[]" value="" style="width:4rem;">
                                            </td>

                                            <td class="d-flex flex-column text-center" style="width:4rem;">
                                                                    <span>Qty</span>
                                                                    <input type="number" class="w-100" name="steel_item_qty[]" onchange="Coating(this)" onkeyup="changeItemAmountCoating(this)" value="0" min="0">
                                                                </td>
                                                                <td class="d-flex flex-column text-center" style="width:4rem;">
                                                                    <span>Width</span>
                                                                    <input type="text" name="item_width[]" onkeyup="prefillArea(this)" value="">
                                                                    <span>Length</span>
                                                                    <input type="text" name="item_length[]" onkeyup="prefillArea(this)" value="">
                                                                </td>

                                            <td class="p-0">
                                                <select style="width:200px;z-index: 2;" class="ms search-select position-relative" name="ral[]" onchange="prefillItemRowSteel(this)">
                                                    <option disabled selected>Pick A RAL</option>
                                                        <?php
                                                            foreach ($powderInventory as $powderItem) {
                                                        ?>
                                                        <option value="<?php echo $powderItem->item_id ?>" attr-data-description="<?php echo $powderItem->powder_description ?>" attr-data-code="<?php echo $powderItem->powder_code ?>" attr-data-cost="<?php echo $powderItem->standard_cost ?>" attr-data-color="<?php echo $powderItem->powder_color ?>" attr-warehouse-id="<?php echo $powderItem->warehouse_id ?>" attr-bin-id="<?php echo $powderItem->bin_id ?>">
                                                            <?php echo $powderItem->powder_color ?>(<?php echo $powderItem->warehouse_name ?>)
                                                            - (<?php echo $powderItem->supplier_name ?>)
                                                        </option>
                                                        <?php
                                                        }
                                                        ?>
                                                </select>
                                            </td>

                                            <td>
                                            <select class="form-control ms" style="width:5rem;" name="size[]" onchange="toggleSizeActive(this)">
                                                                        <option value="LINEAR METRES">LINEAR METRES</option>
                                                                        <option value="LINEAR METRES" data-per="true">LINEAR METRES(Charge Per Linear Mtr)</option>
                                                                        <option value="SQUARE METRES" data-per="true">SQUARE METRES(Charge Per Sq Mtr)</option>
                                                                        <option value="SQUARE METRES">SQUARE METRES</option>
                                                                        <option value="-">INPUT MANUAL</option>
                                                                    </select>

                                                                    <input type="text" class="form-control mt-2" disabled name="size[]" value="">
                                            </td>

                                            <td>
                                                                    <input type="text" class="w-100" name="steel_powder_estimate[]" value="">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="w-100 linear-metre-input" onkeyup="changeItemAmountCoating(this, true)" onchange="changeItemAmountCoating(this, true)" name="sq_metre[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="w-100 sq-metre-input" readonly onkeyup="changeItemAmountCoating(this, true)" onchange="changeItemAmountCoating(this, true)" name="sq_metre[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="w-100" onchange="changeItemAmountCoating(this)" onkeyup="changeItemAmountCoating(this)" name="steel_unit_cost[]" value="">
                                                                </td>
                                            <td>
                                                <select class="ms w-100" name="vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>
                                            <td>
                                                                    <input type="text" onchange="updateCoatingJobGrandTotal()" class="owner-total w-100" name="steel_amount[]" value="">
                                                                </td>
                                            <td>
                                                <button type="button" name="button" class="btn btn-danger" onclick="removeRow(this)">REMOVE</button>
                                                <!-- <button type="button" name="button" onclick="toggleCollapse(this)"
                                                    class="btn btn-info">
                                                    TOGGLE
                                                </button> -->
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="100%">
                                                <button type="button" name="button" onclick="addItemRow('.steel-table')" class="btn btn-danger">ADD ITEM</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="table-responsive mt-3 w-80 direct-sale" style="overflow-x: auto;display:block;overflow:initial;">
                                <table class="table table-bordered col w-100 mx-auto">
                                    <tbody class="item-list maruti-direct">
                                        <tr>
                                            <th class="p-0 border text-center">Item</th>
                                            <th class="p-0 border text-center">Description</th>
                                            <th class="p-0 border text-center">Quantity</th>
                                            <th class="p-0 border text-center">KG</th>
                                            <th class="p-0 border text-center">Boxes</th>
                                            <th class="p-0 border text-center">Unit Cost</th>
                                            <th class="p-0 border text-center">Vat Inclusive</th>
                                            <th class="p-0 border text-center">Total Amt</th>
                                            <th class="p-0 border text-center">Action</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="item_id[]" value="">
                                                <input type="hidden" name="inventory_type[]" value="">
                                                <input type="hidden" name="warehouse_id[]" value="">
                                                <input type="hidden" name="bin_id[]" value="">
                                                <input type="hidden" onkeyup="calculateItemRowTotal(this)" name="item_tax[]" value="<?php echo $vat->percentage ?? '' ?>">

                                                <select style="width:200px;" class="search-select ms" onchange="prefillItemRow(this, true, true, true)" data-style="text-white" data-live-search="true">
                                                    <option disabled selected>Choose from inventory</option>

                                                    <optgroup label="HARDWARE">
                                                        <?php
                                                        foreach ($hardwareInventory as $hardwareItem) {
                                                        ?>
                                                            <option value="<?php echo $hardwareItem->item_id ?>" attr-data-description="<?php echo $hardwareItem->item_description ?>" attr-data-code="<?php echo $hardwareItem->item_code ?>" attr-data-cost="<?php echo $hardwareItem->standard_cost ?>" attr-current-quantity="<?php echo $hardwareItem->current_quantity ?>" attr-min-threshold="<?php echo $hardwareItem->min_threshold ?>" attr-data-name="<?php echo $hardwareItem->item_name ?>" attr-warehouse-id="<?php echo $hardwareItem->warehouse_id ?>" attr-bin-id="<?php echo $hardwareItem->bin_id ?>" attr-weight="<?php echo $hardwareItem->weight_per_kg ?>">
                                                                <?php echo $hardwareItem->item_name ?>-(<?php echo $hardwareItem->current_quantity . " " . $hardwareItem->quantity_tag ?>)
                                                                - (<?php echo $hardwareItem->warehouse_name ?>)
                                                            </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </optgroup>

                                                    <optgroup label="ALUMINIUM">
                                                        <?php
                                                        foreach ($aluminiumInventory as $aluminiumItem) {
                                                        ?>
                                                            <option value="<?php echo $aluminiumItem->item_id ?>" attr-data-description="<?php echo $aluminiumItem->item_description ?>" attr-data-code="<?php echo $aluminiumItem->item_code ?>" attr-data-cost="<?php echo $aluminiumItem->standard_cost ?>" attr-current-quantity="<?php echo $aluminiumItem->current_quantity ?>" attr-min-threshold="<?php echo $aluminiumItem->min_threshold ?>" attr-data-name="<?php echo $aluminiumItem->item_name ?>" attr-warehouse-id="<?php echo $aluminiumItem->warehouse_id ?>" attr-bin-id="<?php echo $aluminiumItem->bin_id ?>" attr-weight="<?php echo $aluminiumItem->weight_per_kg ?>">
                                                                <?php echo $aluminiumItem->item_name ?>-(<?php echo $aluminiumItem->current_quantity . " " . $aluminiumItem->quantity_tag ?>)
                                                                - (<?php echo $aluminiumItem->warehouse_name ?>)
                                                            </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </optgroup>

                                                    <optgroup label="POWDER">
                                                        <?php
                                                        foreach ($powderInventory as $powderItem) {
                                                        ?>
                                                            <option value="<?php echo $powderItem->id ?>" attr-data-description="<?php echo $powderItem->powder_description ?>" attr-data-code="<?php echo $powderItem->powder_code ?>" attr-data-cost="<?php echo $powderItem->taxed_price ?>" attr-current-quantity="<?php echo $powderItem->current_weight ?>" attr-min-threshold="<?php echo $powderItem->min_threshold ?>" attr-data-name="<?php echo $powderItem->powder_color ?>" attr-warehouse-id="<?php echo $powderItem->warehouse_id ?>" attr-bin-id="<?php echo $powderItem->bin_id ?>" attr-weight="<?php echo $powderItem->goods_weight ?>">
                                                                <?php echo $powderItem->powder_color ?>-(<?php echo $powderItem->current_weight . " KG" ?>)
                                                                - (<?php echo $powderItem->warehouse_name ?>)
                                                            </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </optgroup>


                                                </select>
                                                <input type="hidden" class="w-100" readonly name="item_name[]" value="">
                                                <input type="hidden" readonly name="item_code[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" class="w-100" name="item_description[]" value="">
                                            </td>

                                            <td>
                                                <input type="number" min="1" style="width:4rem;" onkeyup="calculateItemRowTotal(this, true, true)" oninput="calculateItemRowTotal(this, true, true)" name="item_qty[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:4rem;" readonly onkeyup="calculateItemRowTotal(this)" name="item_kg[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" style="width:4rem;" name="item_boxes[]" value="">
                                            </td>

                                            <td>
                                                <input type="text" onkeyup="calculateItemRowTotal(this)" name="unit_cost[]" class="w-100" value="">
                                            </td>

                                            <td>
                                                <select class="w-100 ms" name="vat_inclusive[]">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" class="maruti-total w-100" name="amount[]" readonly>
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

                            <div class="row mt-3">

                                <div class="col-sm-6">
                                    <label>Prepared By</label>
                                    <input type="hidden" name="prepared_by" value="{{ $currentlyLogged->id }}">
                                    <input type="text" readonly name="username" class="form-control" value="{{ $currentlyLogged->username }}">
                                </div>

                                <div class="col-sm-6">
                                    <label>Approved By</label>
                                    <select class="form-control search-select ms" name="approved_by">
                                        @foreach($users as $systemUser)
                                        <option value="{{ $systemUser->id }}">{{ $systemUser->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-sm-6">
                                    <label>Supervisor</label>
                                    <select class="form-control search-select ms" name="supervisor">
                                        @foreach($users as $systemUser)
                                        @if($systemUser->id != $currentlyLogged->id)
                                        <option value="{{ $systemUser->id }}">{{ $systemUser->username }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label>Quality By</label>
                                    <select class="form-control search-select ms" name="quality_by">
                                        @foreach($users as $systemUser)
                                        @if($systemUser->id != $currentlyLogged->id)
                                        <option value="{{ $systemUser->id }}">{{ $systemUser->username }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label>Sale By</label>
                                    <select class="form-control search-select ms" name="sale_by">
                                        @foreach($users as $systemUser)
                                        @if($systemUser->id != $currentlyLogged->id)
                                        <option value="{{ $systemUser->id }}">{{ $systemUser->username }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <button type="submit" name="submit_btn" value="Create Invoice" class="btn btn-success text-white w-100 rounded-pill my-4">CREATE INVOICE</button>


                        </form>

                    </div>
                    <script>
                        const todayDate = new Date();
                        const todayDateDefault = document.querySelectorAll('.data-today-date');
                        todayDateDefault.forEach((dateInput) => {
                            dateInput.value = todayDate.toLocaleDateString('en-CA');
                        })
                    </script>
                </div>
            </div>
        </div>
    </section>
    <!-- modal for creating customer on the fly -->
    <div class="modal fade" id="createCustomerForm" tabindex="-1" role="dialog" aria-labelledby="createCustomerForm" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create A Customer in the System</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="tim-icons icon-simple-remove"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('customers.store') }}">
                        @csrf
                        <div class="d-flex flex-column">

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customerName">Customer Name</label>
                                        <input type="text" required name="customer_name" class="form-control" id="customerName" aria-describedby="customerName" class="dark-text" placeholder="Enter customer name">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="kra">KRA Pin</label>
                                        <input type="text" name="kra_pin" class="form-control" id="kra" aria-describedby="customerName" class="dark-text" placeholder="Enter kra pin">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="creditLimit">Credit Limit</label>
                                        <input type="text" required name="credit_limit" class="form-control" id="creditLimit" aria-describedby="creditLimit" class="dark-text" placeholder="Enter credit limit">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="contactNumber">Contact Number</label>
                                        <input type="text" required name="contact_number" class="form-control" id="contactNumber" aria-describedby="contactNumber" class="dark-text" placeholder="Enter contact number">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" required name="location" class="form-control" id="location" aria-describedby="location" class="dark-text" placeholder="Enter location">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="company">Company</label>
                                        <input type="text" required name="company" class="form-control" id="company" aria-describedby="company" class="dark-text" placeholder="Enter company location">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="contactName">Contact Person Name</label>
                                        <input type="text" required name="contact_person_name" class="form-control" id="contactName" aria-describedby="contactName" class="dark-text" placeholder="Enter contact person name">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="contactEmail">Contact Person Email</label>
                                        <input type="email" required name="contact_person_email" class="form-control" id="contactEmail" aria-describedby="contactEmail" class="dark-text" placeholder="Enter contact person email">
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                            <button type="button" onclick="addCustomerViaAPI(this)" name="submit_btn" value="Create Customer" class="btn btn-success">CREATE CUSTOMER</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function resetJobCards(selectElement) {
            const jobCardOptions = document.querySelectorAll('.job-card-option');
            jobCardOptions.forEach((option)=>{
                const customerID = option.getAttribute('data-customer-id');
                if(customerID == selectElement.value){
                    option.disabled = false;
                }else{
                    option.disabled = true;
                }
            }, selectElement);
        }
    </script>

    @include('universal-layout.scripts',
    [
    'libscripts' => true,
    'vendorscripts' => true,
    'mainscripts' => true,
    'bootstrapselect' => true,
    'select2bs4' => true,
    'coating' => true,
    'onTheFlyApi' => true,
    ]
    )
    @include('universal-layout.footer')